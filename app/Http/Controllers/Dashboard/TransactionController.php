<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Transaction::whereHas('account', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('account');

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('transaction_date', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('transaction_date', '<=', $request->to_date);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Filter by account
        if ($request->has('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        $transactions = $query->latest('transaction_date')->paginate(20);

        return Inertia::render('dashboard/Transactions', [
            'transactions' => $transactions,
            'accounts' => $request->user()->accounts,
            'filters' => $request->only(['from_date', 'to_date', 'category', 'account_id']),
        ]);
    }
}
