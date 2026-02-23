<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\CardRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CardRequestController extends Controller
{
    public function index()
    {
        $requests = CardRequest::where('user_id', auth()->id())
            ->with(['account', 'createdCard', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('dashboard/CardRequests', [
            'requests' => $requests,
        ]);
    }

    public function create()
    {
        // Get user's active accounts
        $accounts = Account::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();

        return Inertia::render('dashboard/CardRequestCreate', [
            'accounts' => $accounts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'card_type' => 'required|in:debit,credit',
            'card_brand' => 'required|in:visa,mastercard,amex,discover',
            'purpose' => 'nullable|string|max:1000',
        ]);

        // Ensure the account belongs to the user
        $account = Account::where('id', $validated['account_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $cardRequest = CardRequest::create([
            'user_id' => auth()->id(),
            'account_id' => $validated['account_id'],
            'card_type' => $validated['card_type'],
            'card_brand' => $validated['card_brand'],
            'purpose' => $validated['purpose'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('dashboard.card-requests.index')
            ->with('success', 'Card request submitted successfully. You will be notified once it is reviewed.');
    }

    public function show(CardRequest $cardRequest)
    {
        // Ensure user can only view their own requests
        if ($cardRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $cardRequest->load(['account', 'createdCard', 'approvedBy']);

        return Inertia::render('dashboard/CardRequestShow', [
            'request' => $cardRequest,
        ]);
    }
}
