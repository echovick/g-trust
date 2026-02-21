<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends AdminController
{
    public function index()
    {
        $stats = [
            // User Stats
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),

            // Account Stats
            'total_accounts' => Account::count(),
            'active_accounts' => Account::where('is_active', true)->count(),
            'total_balance' => Account::sum('balance'),
            'total_available_balance' => Account::sum('available_balance'),

            // Transaction Stats
            'total_transactions' => Transaction::count(),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
            'today_transactions' => Transaction::whereDate('transaction_date', today())->count(),
            'today_transaction_volume' => Transaction::where('status', 'completed')
                ->whereDate('transaction_date', today())
                ->sum('amount'),

            // Transfer Stats
            'total_transfers' => Transfer::count(),
            'pending_transfers' => Transfer::where('status', 'pending')->count(),
            'today_transfers' => Transfer::whereDate('created_at', today())->count(),
            'today_transfer_volume' => Transfer::where('status', 'completed')
                ->whereDate('created_at', today())
                ->sum('amount'),

            // Recent Activity
            'recent_transactions' => Transaction::with(['account.user'])
                ->latest('transaction_date')
                ->limit(10)
                ->get(),
            'recent_transfers' => Transfer::with(['fromAccount.user', 'toAccount.user'])
                ->latest()
                ->limit(5)
                ->get(),
            'recent_users' => User::with('accounts')
                ->latest()
                ->limit(5)
                ->get(),

            // Pending Actions
            'pending_approvals' => [
                'transactions' => Transaction::where('status', 'pending')->count(),
                'transfers' => Transfer::where('status', 'pending')->count(),
            ],
        ];

        return Inertia::render('admin/Dashboard', $stats);
    }
}
