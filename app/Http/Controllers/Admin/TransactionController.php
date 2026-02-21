<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransactionController extends AdminController
{
    public function index(Request $request)
    {
        $transactions = Transaction::query()
            ->with(['account.user'])
            ->when($request->search, function ($query, $search) {
                $query->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('account.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('transaction_type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->category, function ($query, $category) {
                $query->where('category', $category);
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                $query->whereDate('transaction_date', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                $query->whereDate('transaction_date', '<=', $dateTo);
            })
            ->latest('transaction_date')
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_transactions' => Transaction::count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
            'total_volume' => Transaction::where('status', 'completed')->sum('amount'),
            'today_volume' => Transaction::where('status', 'completed')
                ->whereDate('transaction_date', today())
                ->sum('amount'),
        ];

        return Inertia::render('admin/transactions/Index', [
            'transactions' => $transactions,
            'stats' => $stats,
            'filters' => $request->only(['search', 'type', 'status', 'category', 'date_from', 'date_to']),
        ]);
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['account.user', 'relatedAccount']);

        return Inertia::render('admin/transactions/Show', [
            'transaction' => $transaction,
        ]);
    }

    public function approve(Request $request, Transaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return back()->withErrors(['status' => 'Only pending transactions can be approved.']);
        }

        $validated = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($transaction, $validated) {
            $account = $transaction->account;

            if ($transaction->transaction_type === 'debit') {
                // Check sufficient balance
                if ($account->available_balance < $transaction->amount) {
                    throw new \Exception('Insufficient balance in account.');
                }

                $account->balance -= $transaction->amount;
                $account->available_balance -= $transaction->amount;
            } else {
                $account->balance += $transaction->amount;
                $account->available_balance += $transaction->amount;
            }

            $account->save();

            $transaction->update([
                'status' => 'completed',
                'balance_after' => $account->balance,
            ]);

            // Log admin action (we'll implement this in Priority 3)
        });

        return back()->with('success', 'Transaction approved successfully.');
    }

    public function reject(Request $request, Transaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return back()->withErrors(['status' => 'Only pending transactions can be rejected.']);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $transaction->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Transaction rejected successfully.');
    }

    public function reverse(Request $request, Transaction $transaction)
    {
        if ($transaction->status !== 'completed') {
            return back()->withErrors(['status' => 'Only completed transactions can be reversed.']);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($transaction, $validated) {
            $account = $transaction->account;

            // Reverse the transaction
            if ($transaction->transaction_type === 'debit') {
                // Refund the amount
                $account->balance += $transaction->amount;
                $account->available_balance += $transaction->amount;
            } else {
                // Deduct the amount
                $account->balance -= $transaction->amount;
                $account->available_balance -= $transaction->amount;
            }

            $account->save();

            // Create a reversal transaction
            Transaction::create([
                'account_id' => $account->id,
                'transaction_type' => $transaction->transaction_type === 'debit' ? 'credit' : 'debit',
                'category' => 'reversal',
                'description' => 'Reversal: ' . $transaction->description,
                'amount' => $transaction->amount,
                'currency' => $transaction->currency,
                'balance_after' => $account->balance,
                'reference_number' => 'REV-' . $transaction->reference_number,
                'status' => 'completed',
                'transaction_date' => now(),
            ]);

            // Mark original transaction as reversed
            $transaction->update([
                'status' => 'cancelled',
            ]);
        });

        return back()->with('success', 'Transaction reversed successfully.');
    }

    public function create()
    {
        $accounts = Account::with('user')
            ->where('is_active', true)
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'account_number' => $account->account_number,
                    'account_name' => $account->account_name,
                    'currency' => $account->currency,
                    'balance' => $account->balance,
                    'available_balance' => $account->available_balance,
                    'user_name' => $account->user->name,
                    'user_email' => $account->user->email,
                ];
            });

        return Inertia::render('admin/transactions/Create', [
            'accounts' => $accounts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'transaction_type' => ['required', 'in:debit,credit'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'in:pending,completed'],
        ]);

        $account = Account::findOrFail($validated['account_id']);

        // Check if sufficient balance for debit transactions
        if ($validated['transaction_type'] === 'debit' && $validated['status'] === 'completed') {
            if ($account->available_balance < $validated['amount']) {
                return back()->withErrors(['amount' => 'Insufficient balance in account.']);
            }
        }

        DB::transaction(function () use ($account, $validated) {
            $balanceAfter = $account->balance;

            // Update balance only if status is completed
            if ($validated['status'] === 'completed') {
                if ($validated['transaction_type'] === 'debit') {
                    $account->balance -= $validated['amount'];
                    $account->available_balance -= $validated['amount'];
                } else {
                    $account->balance += $validated['amount'];
                    $account->available_balance += $validated['amount'];
                }
                $account->save();
                $balanceAfter = $account->balance;
            }

            // Create transaction record
            Transaction::create([
                'account_id' => $account->id,
                'transaction_type' => $validated['transaction_type'],
                'category' => $validated['category'],
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'currency' => $account->currency,
                'balance_after' => $balanceAfter,
                'reference_number' => 'TXN-' . strtoupper(uniqid()),
                'status' => $validated['status'],
                'transaction_date' => now(),
            ]);
        });

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction created successfully.');
    }

    public function edit(Transaction $transaction)
    {
        // Only allow editing of pending transactions
        if ($transaction->status !== 'pending') {
            return redirect()->route('admin.transactions.show', $transaction)
                ->withErrors(['status' => 'Only pending transactions can be edited.']);
        }

        $transaction->load('account.user');

        return Inertia::render('admin/transactions/Edit', [
            'transaction' => $transaction,
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        // Only allow updating of pending transactions
        if ($transaction->status !== 'pending') {
            return back()->withErrors(['status' => 'Only pending transactions can be updated.']);
        }

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        // Check balance if changing amount for debit transactions
        if ($validated['amount'] != $transaction->amount && $transaction->transaction_type === 'debit') {
            $account = $transaction->account;
            if ($account->available_balance < $validated['amount']) {
                return back()->withErrors(['amount' => 'Insufficient balance in account for this amount.']);
            }
        }

        $transaction->update($validated);

        return back()->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        // Only allow deletion of pending or cancelled transactions
        if (!in_array($transaction->status, ['pending', 'cancelled'])) {
            return back()->withErrors(['status' => 'Only pending or cancelled transactions can be deleted. Use reverse for completed transactions.']);
        }

        $transaction->delete();

        return redirect()->route('admin.transactions.index')->with('success', 'Transaction deleted successfully.');
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'transaction_ids' => ['required', 'array', 'min:1'],
            'transaction_ids.*' => ['required', 'exists:transactions,id'],
        ]);

        $transactions = Transaction::with('account')
            ->whereIn('id', $validated['transaction_ids'])
            ->where('status', 'pending')
            ->get();

        $approved = 0;
        $skipped = 0;
        $errors = [];

        DB::transaction(function () use ($transactions, &$approved, &$skipped, &$errors) {
            foreach ($transactions as $transaction) {
                try {
                    $account = $transaction->account;

                    if ($transaction->transaction_type === 'debit') {
                        // Check sufficient balance
                        if ($account->available_balance < $transaction->amount) {
                            $skipped++;
                            $errors[] = "Transaction {$transaction->reference_number}: Insufficient balance.";
                            continue;
                        }

                        $account->balance -= $transaction->amount;
                        $account->available_balance -= $transaction->amount;
                    } else {
                        $account->balance += $transaction->amount;
                        $account->available_balance += $transaction->amount;
                    }

                    $account->save();

                    $transaction->update([
                        'status' => 'completed',
                        'balance_after' => $account->balance,
                    ]);

                    $approved++;
                } catch (\Exception $e) {
                    $skipped++;
                    $errors[] = "Transaction {$transaction->reference_number}: {$e->getMessage()}";
                }
            }
        });

        $message = "{$approved} transaction(s) approved.";
        if ($skipped > 0) {
            $message .= " {$skipped} transaction(s) skipped.";
        }

        return back()->with('success', $message)->withErrors($errors);
    }

    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'transaction_ids' => ['required', 'array', 'min:1'],
            'transaction_ids.*' => ['required', 'exists:transactions,id'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $rejected = Transaction::whereIn('id', $validated['transaction_ids'])
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        return back()->with('success', "{$rejected} transaction(s) rejected.");
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'transaction_ids' => ['required', 'array', 'min:1'],
            'transaction_ids.*' => ['required', 'exists:transactions,id'],
        ]);

        $transactions = Transaction::whereIn('id', $validated['transaction_ids'])->get();
        $deleted = 0;
        $skipped = 0;
        $errors = [];

        foreach ($transactions as $transaction) {
            if (!in_array($transaction->status, ['pending', 'cancelled'])) {
                $skipped++;
                $errors[] = "Transaction {$transaction->reference_number} is {$transaction->status} and cannot be deleted.";
                continue;
            }

            $transaction->delete();
            $deleted++;
        }

        $message = "{$deleted} transaction(s) deleted.";
        if ($skipped > 0) {
            $message .= " {$skipped} transaction(s) skipped.";
        }

        return back()->with('success', $message)->withErrors($errors);
    }

    public function bulkExport(Request $request)
    {
        $validated = $request->validate([
            'transaction_ids' => ['nullable', 'array'],
            'transaction_ids.*' => ['exists:transactions,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'status' => ['nullable', 'in:pending,completed,failed,cancelled'],
            'type' => ['nullable', 'in:debit,credit'],
        ]);

        $query = Transaction::with('account.user');

        if (!empty($validated['transaction_ids'])) {
            $query->whereIn('id', $validated['transaction_ids']);
        }

        if (!empty($validated['date_from'])) {
            $query->whereDate('transaction_date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('transaction_date', '<=', $validated['date_to']);
        }

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['type'])) {
            $query->where('transaction_type', $validated['type']);
        }

        $transactions = $query->latest('transaction_date')->get();

        $csv = "Reference Number,Account Number,User Name,Type,Category,Description,Amount,Currency,Balance After,Status,Transaction Date,Created At\n";

        foreach ($transactions as $transaction) {
            $csv .= implode(',', [
                $transaction->reference_number,
                $transaction->account->account_number,
                '"' . $transaction->account->user->name . '"',
                $transaction->transaction_type,
                $transaction->category,
                '"' . str_replace('"', '""', $transaction->description) . '"',
                $transaction->amount,
                $transaction->currency,
                $transaction->balance_after,
                $transaction->status,
                $transaction->transaction_date->format('Y-m-d H:i:s'),
                $transaction->created_at->format('Y-m-d H:i:s'),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="transactions-export-' . now()->format('Y-m-d-His') . '.csv"',
        ]);
    }
}
