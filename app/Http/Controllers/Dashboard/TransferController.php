<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TransferController extends Controller
{
    public function index(Request $request): Response
    {
        $transfers = Transfer::where('from_account_id', function ($query) use ($request) {
                $query->select('id')
                    ->from('accounts')
                    ->where('user_id', $request->user()->id);
            })
            ->orWhere('to_account_id', function ($query) use ($request) {
                $query->select('id')
                    ->from('accounts')
                    ->where('user_id', $request->user()->id);
            })
            ->with(['fromAccount', 'toAccount', 'beneficiary'])
            ->latest('created_at')
            ->paginate(20);

        return Inertia::render('dashboard/Transfers', [
            'transfers' => $transfers,
            'accounts' => $request->user()->accounts,
            'beneficiaries' => $request->user()->beneficiaries,
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('dashboard/TransferCreate', [
            'accounts' => $request->user()->accounts,
            'beneficiaries' => $request->user()->beneficiaries,
        ]);
    }
}
