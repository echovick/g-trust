<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepositController extends Controller
{
    public function index()
    {
        $deposits = Deposit::whereHas('account', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with('account')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('dashboard/Deposits', [
            'deposits' => $deposits,
        ]);
    }

    public function create()
    {
        $accounts = Account::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();

        return Inertia::render('dashboard/DepositCreate', [
            'accounts' => $accounts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'check_number' => 'nullable|string|max:255',
            'check_front_image' => 'required|image|max:5120',
            'check_back_image' => 'required|image|max:5120',
            'memo' => 'nullable|string|max:500',
        ]);

        // Ensure the account belongs to the user
        $account = Account::where('id', $validated['account_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Upload check images
        $frontImagePath = $request->file('check_front_image')->store('deposits', 'public');
        $backImagePath = $request->file('check_back_image')->store('deposits', 'public');

        Deposit::create([
            'account_id' => $validated['account_id'],
            'deposit_type' => 'check',
            'amount' => $validated['amount'],
            'currency' => $account->currency,
            'reference_number' => 'DEP-' . strtoupper(uniqid()),
            'check_number' => $validated['check_number'] ?? null,
            'check_front_image' => $frontImagePath,
            'check_back_image' => $backImagePath,
            'memo' => $validated['memo'] ?? null,
            'status' => 'pending',
            'deposit_date' => now(),
        ]);

        return redirect()->route('dashboard.deposits.index')
            ->with('success', 'Check deposit submitted successfully. It will be reviewed within 1-2 business days.');
    }
}
