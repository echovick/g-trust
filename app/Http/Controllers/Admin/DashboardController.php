<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends AdminController
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_accounts' => Account::count(),
            'total_balance' => Account::sum('balance'),
            'recent_transactions' => Transaction::with(['account.user'])
                ->latest()
                ->limit(10)
                ->get(),
            'recent_users' => User::with('accounts')
                ->latest()
                ->limit(5)
                ->get(),
        ];

        return Inertia::render('admin/Dashboard', $stats);
    }
}
