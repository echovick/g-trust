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
