<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Get user's accounts
        $accounts = $user->accounts()
            ->where('is_active', true)
            ->with(['transactions' => function ($query) {
                $query->latest('transaction_date')->limit(5);
            }])
            ->get();

        // Get recent transactions across all accounts
        $recentTransactions = Transaction::whereHas('account', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('account')
            ->latest('transaction_date')
            ->limit(10)
            ->get();

        // Calculate total balance across all accounts
        $totalBalance = $accounts->sum('balance');

        // Monthly spending (last 30 days of debits)
        $monthlySpending = Transaction::whereHas('account', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('transaction_type', 'debit')
            ->where('transaction_date', '>=', now()->subDays(30))
            ->sum('amount');

        return Inertia::render('Dashboard', [
            'accounts' => $accounts,
            'recentTransactions' => $recentTransactions,
            'totalBalance' => $totalBalance,
            'monthlySpending' => abs($monthlySpending),
            'preferredCurrency' => $user->preferred_currency ?? 'USD',
        ]);
    }

    public function updateCurrency(Request $request)
    {
        $request->validate([
            'currency' => 'required|in:USD,EUR,GBP',
        ]);

        $request->user()->update([
            'preferred_currency' => $request->currency,
        ]);

        return back();
    }
}
