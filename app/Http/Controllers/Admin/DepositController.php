<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DepositController extends Controller
{
    public function index(Request $request)
    {
        $query = Deposit::with(['account.user'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search by user name or email
        if ($request->has('search') && $request->search) {
            $query->whereHas('account.user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $deposits = $query->paginate(20);

        $stats = [
            'total_deposits' => Deposit::count(),
            'pending_deposits' => Deposit::where('status', 'pending')->count(),
            'approved_deposits' => Deposit::where('status', 'approved')->count(),
            'rejected_deposits' => Deposit::where('status', 'rejected')->count(),
        ];

        return Inertia::render('admin/deposits/Index', [
            'deposits' => $deposits,
            'stats' => $stats,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    public function show(Deposit $deposit)
    {
        $deposit->load(['account.user']);

        return Inertia::render('admin/deposits/Show', [
            'deposit' => $deposit,
        ]);
    }

    public function approve(Deposit $deposit)
    {
        if ($deposit->status !== 'pending') {
            return back()->with('error', 'Only pending deposits can be approved.');
        }

        DB::transaction(function () use ($deposit) {
            $account = $deposit->account;
            $amount = $deposit->amount;

            // Credit the account
            $account->balance += $amount;
            $account->available_balance += $amount;
            $account->save();

            // Create transaction record
            Transaction::create([
                'account_id' => $account->id,
                'transaction_type' => 'credit',
                'category' => 'deposit',
                'description' => 'Check deposit - Ref: ' . $deposit->reference_number,
                'amount' => $amount,
                'currency' => $deposit->currency,
                'balance_after' => $account->balance,
                'reference_number' => $deposit->reference_number,
                'status' => 'completed',
                'transaction_date' => now(),
            ]);

            // Update deposit status and set available date (2 business days)
            $availableDate = now()->addWeekdays(2);

            $deposit->update([
                'status' => 'approved',
                'available_date' => $availableDate,
            ]);
        });

        return redirect()->route('admin.deposits.index')
            ->with('success', 'Deposit approved and account credited successfully.');
    }

    public function reject(Request $request, Deposit $deposit)
    {
        if ($deposit->status !== 'pending') {
            return back()->with('error', 'Only pending deposits can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $deposit->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return redirect()->route('admin.deposits.index')
            ->with('success', 'Deposit rejected successfully.');
    }
}
