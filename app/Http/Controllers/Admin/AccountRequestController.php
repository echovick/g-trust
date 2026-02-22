<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AccountRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountRequest::with(['user', 'createdAccount', 'approvedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search by user name or email
        if ($request->has('search') && $request->search) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $requests = $query->paginate(20);

        $stats = [
            'total_requests' => AccountRequest::count(),
            'pending_requests' => AccountRequest::where('status', 'pending')->count(),
            'approved_requests' => AccountRequest::where('status', 'approved')->count(),
            'rejected_requests' => AccountRequest::where('status', 'rejected')->count(),
        ];

        return Inertia::render('admin/account-requests/Index', [
            'requests' => $requests,
            'stats' => $stats,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    public function show(AccountRequest $accountRequest)
    {
        $accountRequest->load(['user', 'createdAccount', 'approvedBy']);

        return Inertia::render('admin/account-requests/Show', [
            'accountRequest' => $accountRequest,
        ]);
    }

    public function approve(AccountRequest $accountRequest)
    {
        if ($accountRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        // Generate unique account number
        $accountNumber = $this->generateAccountNumber();

        // Create the account
        $account = Account::create([
            'user_id' => $accountRequest->user_id,
            'account_number' => $accountNumber,
            'account_name' => $accountRequest->account_name,
            'account_type' => $accountRequest->account_type,
            'currency' => $accountRequest->currency,
            'balance' => 0,
            'available_balance' => 0,
            'is_active' => true,
            'is_primary' => Account::where('user_id', $accountRequest->user_id)->count() === 0,
        ]);

        // Update the request
        $accountRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'created_account_id' => $account->id,
        ]);

        return redirect()->route('admin.account-requests.index')
            ->with('success', "Account request approved. Account number: {$accountNumber}");
    }

    public function reject(Request $request, AccountRequest $accountRequest)
    {
        if ($accountRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $accountRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_at' => now(),
        ]);

        return redirect()->route('admin.account-requests.index')
            ->with('success', 'Account request rejected successfully.');
    }

    private function generateAccountNumber(): string
    {
        // Generate a unique 10-digit account number
        do {
            $accountNumber = '10' . str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        } while (Account::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }
}
