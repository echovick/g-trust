<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountRequestController extends Controller
{
    public function index()
    {
        $requests = AccountRequest::where('user_id', auth()->id())
            ->with(['createdAccount', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('dashboard/AccountRequests', [
            'requests' => $requests,
        ]);
    }

    public function create()
    {
        return Inertia::render('dashboard/AccountRequestCreate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_type' => 'required|in:checking,savings,business',
            'currency' => 'required|in:USD,EUR,GBP,NGN',
            'account_name' => 'required|string|max:255',
            'purpose' => 'nullable|string|max:1000',
        ]);

        $accountRequest = AccountRequest::create([
            'user_id' => auth()->id(),
            'account_type' => $validated['account_type'],
            'currency' => $validated['currency'],
            'account_name' => $validated['account_name'],
            'purpose' => $validated['purpose'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard.account-requests.index')
            ->with('success', 'Account request submitted successfully. You will be notified once it is reviewed.');
    }

    public function show(AccountRequest $accountRequest)
    {
        // Ensure user can only view their own requests
        if ($accountRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $accountRequest->load(['createdAccount', 'approvedBy']);

        return Inertia::render('dashboard/AccountRequestShow', [
            'request' => $accountRequest,
        ]);
    }
}
