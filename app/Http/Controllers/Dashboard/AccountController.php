<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(Request $request): Response
    {
        $accounts = $request->user()
            ->accounts()
            ->with(['cards', 'transactions' => function ($query) {
                $query->latest('transaction_date')->limit(5);
            }])
            ->get();

        return Inertia::render('dashboard/Accounts', [
            'accounts' => $accounts,
        ]);
    }

    public function show(Request $request, Account $account): Response
    {
        // Ensure the account belongs to the authenticated user
        if ($account->user_id !== $request->user()->id) {
            abort(403);
        }

        $account->load([
            'transactions' => function ($query) {
                $query->latest('transaction_date')->paginate(20);
            },
            'cards',
        ]);

        return Inertia::render('dashboard/AccountDetails', [
            'account' => $account,
        ]);
    }
}
