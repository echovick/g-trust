<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transfer;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransferController extends AdminController
{
    public function index(Request $request)
    {
        $transfers = Transfer::query()
            ->with(['fromAccount.user', 'toAccount.user', 'beneficiary'])
            ->when($request->search, function ($query, $search) {
                $query->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('fromAccount.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('transfer_type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->date_from, function ($query, $dateFrom) {
                $query->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($request->date_to, function ($query, $dateTo) {
                $query->whereDate('created_at', '<=', $dateTo);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_transfers' => Transfer::count(),
            'pending_transfers' => Transfer::where('status', 'pending')->count(),
            'total_volume' => Transfer::where('status', 'completed')->sum('amount'),
            'today_volume' => Transfer::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'),
        ];

        return Inertia::render('admin/transfers/Index', [
            'transfers' => $transfers,
            'stats' => $stats,
            'filters' => $request->only(['search', 'type', 'status', 'date_from', 'date_to']),
        ]);
    }

    public function show(Transfer $transfer)
    {
        $transfer->load(['fromAccount.user', 'toAccount.user', 'beneficiary']);

        return Inertia::render('admin/transfers/Show', [
            'transfer' => $transfer,
        ]);
    }

    public function create()
    {
        $accounts = Account::with('user')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('admin/transfers/Create', [
            'accounts' => $accounts,
        ]);
    }

    public function store(Request $request)
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
            'execute_immediately' => ['boolean'],
        ]);

        $fromAccount = Account::findOrFail($validated['from_account_id']);
        $toAccount = Account::findOrFail($validated['to_account_id']);

        // Check if from_account has sufficient balance
        if ($fromAccount->available_balance < $validated['amount']) {
            return back()->withErrors(['amount' => 'Insufficient balance in source account.']);
        }

        $transfer = DB::transaction(function () use ($fromAccount, $toAccount, $validated) {
            $amount = $validated['amount'];
            $description = $validated['description'];
            $referenceNumber = 'ADM-TRF-' . strtoupper(uniqid());

            // Calculate exchange if currencies are different
            $exchangeRate = 1;
            $convertedAmount = $amount;

            if ($fromAccount->currency !== $toAccount->currency) {
                $exchangeRate = $this->getExchangeRate($fromAccount->currency, $toAccount->currency);
                $convertedAmount = $amount * $exchangeRate;
            }

            if ($validated['execute_immediately'] ?? true) {
                // Execute transfer immediately
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

                $status = 'completed';
                $completedAt = now();
            } else {
                $status = 'pending';
                $completedAt = null;
            }

            // Create transfer record
            return Transfer::create([
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
                'status' => $status,
                'completed_at' => $completedAt,
            ]);
        });

        return redirect()->route('admin.transfers.show', $transfer)
            ->with('success', 'Transfer created successfully.');
    }

    public function approve(Request $request, Transfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return back()->withErrors(['status' => 'Only pending transfers can be approved.']);
        }

        $fromAccount = $transfer->fromAccount;
        $toAccount = $transfer->toAccount;

        if ($fromAccount->available_balance < $transfer->amount) {
            return back()->withErrors(['amount' => 'Insufficient balance in source account.']);
        }

        DB::transaction(function () use ($transfer, $fromAccount, $toAccount) {
            $referenceNumber = $transfer->reference_number;

            // Debit from account
            $fromAccount->balance -= $transfer->amount;
            $fromAccount->available_balance -= $transfer->amount;
            $fromAccount->save();

            Transaction::create([
                'account_id' => $fromAccount->id,
                'transaction_type' => 'debit',
                'category' => 'transfer',
                'description' => $transfer->description . ' (To: ' . $toAccount->account_number . ')',
                'amount' => $transfer->amount,
                'currency' => $fromAccount->currency,
                'balance_after' => $fromAccount->balance,
                'reference_number' => $referenceNumber,
                'status' => 'completed',
                'related_account_id' => $toAccount->id,
                'transaction_date' => now(),
            ]);

            // Credit to account
            $toAccount->balance += $transfer->converted_amount;
            $toAccount->available_balance += $transfer->converted_amount;
            $toAccount->save();

            Transaction::create([
                'account_id' => $toAccount->id,
                'transaction_type' => 'credit',
                'category' => 'transfer',
                'description' => $transfer->description . ' (From: ' . $fromAccount->account_number . ')',
                'amount' => $transfer->converted_amount,
                'currency' => $toAccount->currency,
                'balance_after' => $toAccount->balance,
                'reference_number' => $referenceNumber,
                'status' => 'completed',
                'related_account_id' => $fromAccount->id,
                'transaction_date' => now(),
            ]);

            $transfer->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        });

        return back()->with('success', 'Transfer approved successfully.');
    }

    public function cancel(Request $request, Transfer $transfer)
    {
        if (!in_array($transfer->status, ['pending', 'scheduled'])) {
            return back()->withErrors(['status' => 'Only pending or scheduled transfers can be cancelled.']);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $transfer->update([
            'status' => 'failed',
        ]);

        return back()->with('success', 'Transfer cancelled successfully.');
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
