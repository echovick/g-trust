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
}
