<?php

namespace App\Http\Controllers\Admin;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class AccountController extends AdminController
{
    public function index(Request $request)
    {
        $accounts = Account::query()
            ->with('user')
            ->when($request->search, function ($query, $search) {
                $query->where('account_number', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/accounts/Index', [
            'accounts' => $accounts,
            'filters' => $request->only(['search']),
        ]);
    }

    public function show(Account $account)
    {
        $account->load(['user', 'transactions' => function ($query) {
            $query->latest()->limit(20);
        }]);

        return Inertia::render('admin/accounts/Show', [
            'account' => $account,
        ]);
    }

    public function fundAccount(Request $request, Account $account)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($account, $validated) {
            // Update account balance
            $account->balance += $validated['amount'];
            $account->available_balance += $validated['amount'];
            $account->save();

            // Create transaction record
            Transaction::create([
                'account_id' => $account->id,
                'transaction_type' => 'credit',
                'category' => 'deposit',
                'description' => $validated['description'],
                'amount' => $validated['amount'],
                'currency' => $account->currency,
                'balance_after' => $account->balance,
                'reference_number' => 'ADM-' . strtoupper(uniqid()),
                'status' => 'completed',
                'transaction_date' => now(),
            ]);
        });

        return back()->with('success', 'Account funded successfully.');
    }

    public function intraAccountTransfer(Request $request)
    {
        $validated = $request->validate([
            'from_account_id' => ['required', 'exists:accounts,id'],
            'to_account_id' => [
                'required',
                'exists:accounts,id',
                'different:from_account_id',
            ],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $fromAccount = Account::findOrFail($validated['from_account_id']);
        $toAccount = Account::findOrFail($validated['to_account_id']);

        // Check if from_account has sufficient balance
        if ($fromAccount->available_balance < $validated['amount']) {
            return back()->withErrors(['amount' => 'Insufficient balance in source account.']);
        }

        DB::transaction(function () use ($fromAccount, $toAccount, $validated) {
            $amount = $validated['amount'];
            $description = $validated['description'];
            $referenceNumber = 'TRF-' . strtoupper(uniqid());

            // Calculate exchange if currencies are different
            $exchangeRate = 1;
            $convertedAmount = $amount;

            if ($fromAccount->currency !== $toAccount->currency) {
                // In a real application, you would fetch the exchange rate from an API
                // For now, we'll use a simple mock rate
                $exchangeRate = $this->getExchangeRate($fromAccount->currency, $toAccount->currency);
                $convertedAmount = $amount * $exchangeRate;
            }

            // Debit from account
            $fromAccount->balance -= $amount;
            $fromAccount->available_balance -= $amount;
            $fromAccount->save();

            Transaction::create([
                'account_id' => $fromAccount->id,
                'transaction_type' => 'debit',
                'category' => 'transfer',
                'description' => $description . ' (To: ' . $toAccount->account_number . ')',
                'amount' => $amount,
                'currency' => $fromAccount->currency,
                'balance_after' => $fromAccount->balance,
                'reference_number' => $referenceNumber,
                'status' => 'completed',
                'related_account_id' => $toAccount->id,
                'transaction_date' => now(),
            ]);

            // Credit to account
            $toAccount->balance += $convertedAmount;
            $toAccount->available_balance += $convertedAmount;
            $toAccount->save();

            Transaction::create([
                'account_id' => $toAccount->id,
                'transaction_type' => 'credit',
                'category' => 'transfer',
                'description' => $description . ' (From: ' . $fromAccount->account_number . ')',
                'amount' => $convertedAmount,
                'currency' => $toAccount->currency,
                'balance_after' => $toAccount->balance,
                'reference_number' => $referenceNumber,
                'status' => 'completed',
                'related_account_id' => $fromAccount->id,
                'transaction_date' => now(),
            ]);

            // Create transfer record
            Transfer::create([
                'from_account_id' => $fromAccount->id,
                'to_account_id' => $toAccount->id,
                'transfer_type' => 'internal',
                'amount' => $amount,
                'from_currency' => $fromAccount->currency,
                'to_currency' => $toAccount->currency,
                'exchange_rate' => $exchangeRate,
                'converted_amount' => $convertedAmount,
                'fee' => 0,
                'reference_number' => $referenceNumber,
                'description' => $description,
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        });

        return back()->with('success', 'Transfer completed successfully.');
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);

        return Inertia::render('admin/accounts/Create', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'account_type' => ['required', Rule::in(['checking', 'savings', 'business'])],
            'currency' => ['required', 'string', 'size:3'],
            'account_name' => ['required', 'string', 'max:255'],
            'initial_balance' => ['nullable', 'numeric', 'min:0'],
            'is_primary' => ['boolean'],
        ]);

        DB::transaction(function () use ($validated) {
            // Generate unique account number
            do {
                $accountNumber = 'ACC' . str_pad(rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
            } while (Account::where('account_number', $accountNumber)->exists());

            $initialBalance = $validated['initial_balance'] ?? 0;

            // If this is set as primary, unset other primary accounts for this user
            if ($validated['is_primary'] ?? false) {
                Account::where('user_id', $validated['user_id'])
                    ->update(['is_primary' => false]);
            }

            $account = Account::create([
                'user_id' => $validated['user_id'],
                'account_number' => $accountNumber,
                'account_name' => $validated['account_name'],
                'account_type' => $validated['account_type'],
                'currency' => $validated['currency'],
                'balance' => $initialBalance,
                'available_balance' => $initialBalance,
                'is_primary' => $validated['is_primary'] ?? false,
                'is_active' => true,
            ]);

            // Create initial deposit transaction if balance > 0
            if ($initialBalance > 0) {
                Transaction::create([
                    'account_id' => $account->id,
                    'transaction_type' => 'credit',
                    'category' => 'deposit',
                    'description' => 'Initial account funding',
                    'amount' => $initialBalance,
                    'currency' => $account->currency,
                    'balance_after' => $initialBalance,
                    'reference_number' => 'INIT-' . strtoupper(uniqid()),
                    'status' => 'completed',
                    'transaction_date' => now(),
                ]);
            }
        });

        return redirect()->route('admin.accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(Account $account)
    {
        $account->load('user');

        return Inertia::render('admin/accounts/Edit', [
            'account' => $account,
        ]);
    }

    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'account_name' => ['required', 'string', 'max:255'],
            'account_type' => ['required', Rule::in(['checking', 'savings', 'business'])],
            'is_active' => ['boolean'],
            'is_primary' => ['boolean'],
        ]);

        DB::transaction(function () use ($account, $validated) {
            // If setting as primary, unset other primary accounts for this user
            if (($validated['is_primary'] ?? false) && !$account->is_primary) {
                Account::where('user_id', $account->user_id)
                    ->where('id', '!=', $account->id)
                    ->update(['is_primary' => false]);
            }

            // Prevent deactivation of primary account
            if (isset($validated['is_active']) && !$validated['is_active'] && $account->is_primary) {
                throw new \Exception('Cannot deactivate primary account. Set another account as primary first.');
            }

            $account->update($validated);
        });

        return back()->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        // Check if account has balance
        if ($account->balance > 0 || $account->balance < 0) {
            return back()->withErrors(['account' => 'Cannot delete account with non-zero balance. Current balance: ' . $account->currency . ' ' . $account->balance]);
        }

        // Check if account is primary
        if ($account->is_primary) {
            return back()->withErrors(['account' => 'Cannot delete primary account. Set another account as primary first.']);
        }

        // Check for recent transactions (within last 30 days)
        $recentTransactions = $account->transactions()
            ->where('transaction_date', '>=', now()->subDays(30))
            ->count();

        if ($recentTransactions > 0) {
            return back()->withErrors(['account' => 'Cannot delete account with transactions in the last 30 days.']);
        }

        $account->delete();

        return redirect()->route('admin.accounts.index')->with('success', 'Account deleted successfully.');
    }

    public function toggleStatus(Account $account)
    {
        // Prevent deactivation of primary account
        if ($account->is_active && $account->is_primary) {
            return back()->withErrors(['status' => 'Cannot deactivate primary account. Set another account as primary first.']);
        }

        $account->update([
            'is_active' => !$account->is_active,
        ]);

        $status = $account->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Account {$status} successfully.");
    }

    public function bulkToggleStatus(Request $request)
    {
        $validated = $request->validate([
            'account_ids' => ['required', 'array', 'min:1'],
            'account_ids.*' => ['required', 'exists:accounts,id'],
            'is_active' => ['required', 'boolean'],
        ]);

        $accounts = Account::whereIn('id', $validated['account_ids'])->get();
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($accounts as $account) {
            // Prevent deactivation of primary accounts
            if (!$validated['is_active'] && $account->is_primary) {
                $skipped++;
                $errors[] = "Account {$account->account_number} is primary and cannot be deactivated.";
                continue;
            }

            $account->update(['is_active' => $validated['is_active']]);
            $updated++;
        }

        $status = $validated['is_active'] ? 'activated' : 'deactivated';
        $message = "{$updated} account(s) {$status}.";
        if ($skipped > 0) {
            $message .= " {$skipped} account(s) skipped.";
        }

        return back()->with('success', $message)->withErrors($errors);
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'account_ids' => ['required', 'array', 'min:1'],
            'account_ids.*' => ['required', 'exists:accounts,id'],
        ]);

        $accounts = Account::whereIn('id', $validated['account_ids'])->with('transactions')->get();
        $deleted = 0;
        $skipped = 0;
        $errors = [];

        foreach ($accounts as $account) {
            // Check restrictions
            if ($account->balance != 0) {
                $skipped++;
                $errors[] = "Account {$account->account_number} has non-zero balance.";
                continue;
            }

            if ($account->is_primary) {
                $skipped++;
                $errors[] = "Account {$account->account_number} is primary.";
                continue;
            }

            $recentTransactions = $account->transactions()
                ->where('transaction_date', '>=', now()->subDays(30))
                ->count();

            if ($recentTransactions > 0) {
                $skipped++;
                $errors[] = "Account {$account->account_number} has recent transactions.";
                continue;
            }

            $account->delete();
            $deleted++;
        }

        $message = "{$deleted} account(s) deleted.";
        if ($skipped > 0) {
            $message .= " {$skipped} account(s) skipped.";
        }

        return back()->with('success', $message)->withErrors($errors);
    }

    public function bulkFund(Request $request)
    {
        $validated = $request->validate([
            'account_ids' => ['required', 'array', 'min:1'],
            'account_ids.*' => ['required', 'exists:accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $accounts = Account::whereIn('id', $validated['account_ids'])->get();
        $funded = 0;

        DB::transaction(function () use ($accounts, $validated, &$funded) {
            foreach ($accounts as $account) {
                // Update account balance
                $account->balance += $validated['amount'];
                $account->available_balance += $validated['amount'];
                $account->save();

                // Create transaction record
                Transaction::create([
                    'account_id' => $account->id,
                    'transaction_type' => 'credit',
                    'category' => 'deposit',
                    'description' => $validated['description'],
                    'amount' => $validated['amount'],
                    'currency' => $account->currency,
                    'balance_after' => $account->balance,
                    'reference_number' => 'BULK-' . strtoupper(uniqid()),
                    'status' => 'completed',
                    'transaction_date' => now(),
                ]);

                $funded++;
            }
        });

        return back()->with('success', "{$funded} account(s) funded successfully.");
    }

    public function bulkExport(Request $request)
    {
        $validated = $request->validate([
            'account_ids' => ['nullable', 'array'],
            'account_ids.*' => ['exists:accounts,id'],
        ]);

        $query = Account::with('user');

        if (!empty($validated['account_ids'])) {
            $query->whereIn('id', $validated['account_ids']);
        }

        $accounts = $query->get();

        $csv = "Account Number,Account Name,Account Type,Currency,Balance,Available Balance,Status,Primary,User Name,User Email,Created At\n";

        foreach ($accounts as $account) {
            $csv .= implode(',', [
                $account->account_number,
                '"' . $account->account_name . '"',
                $account->account_type,
                $account->currency,
                $account->balance,
                $account->available_balance,
                $account->is_active ? 'Active' : 'Inactive',
                $account->is_primary ? 'Yes' : 'No',
                '"' . $account->user->name . '"',
                $account->user->email,
                $account->created_at->format('Y-m-d H:i:s'),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="accounts-export-' . now()->format('Y-m-d-His') . '.csv"',
        ]);
    }

    private function getExchangeRate(string $from, string $to): float
    {
        // Mock exchange rates - in production, fetch from a real API
        $rates = [
            'USD_EUR' => 0.92,
            'USD_GBP' => 0.79,
            'USD_NGN' => 1550.00,
            'EUR_USD' => 1.09,
            'EUR_GBP' => 0.86,
            'EUR_NGN' => 1685.00,
            'GBP_USD' => 1.27,
            'GBP_EUR' => 1.16,
            'GBP_NGN' => 1962.00,
            'NGN_USD' => 0.00065,
            'NGN_EUR' => 0.00059,
            'NGN_GBP' => 0.00051,
        ];

        $key = $from . '_' . $to;
        return $rates[$key] ?? 1;
    }
}
