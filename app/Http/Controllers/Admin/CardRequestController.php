<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\CardRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CardRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = CardRequest::with(['user', 'account', 'createdCard', 'approvedBy'])
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
            'total_requests' => CardRequest::count(),
            'pending_requests' => CardRequest::where('status', 'pending')->count(),
            'approved_requests' => CardRequest::where('status', 'approved')->count(),
            'rejected_requests' => CardRequest::where('status', 'rejected')->count(),
        ];

        return Inertia::render('admin/card-requests/Index', [
            'requests' => $requests,
            'stats' => $stats,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
            ],
        ]);
    }

    public function show(CardRequest $cardRequest)
    {
        $cardRequest->load(['user', 'account', 'createdCard', 'approvedBy']);

        return Inertia::render('admin/card-requests/Show', [
            'cardRequest' => $cardRequest,
        ]);
    }

    public function approve(CardRequest $cardRequest)
    {
        if ($cardRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be approved.');
        }

        // Generate unique card number
        $cardNumber = $this->generateCardNumber();

        // Get account details for card holder name
        $account = $cardRequest->account;

        // Set expiry date (5 years from now for credit, 3 years for debit)
        $yearsToAdd = $cardRequest->card_type === 'credit' ? 5 : 3;
        $expiryDate = now()->addYears($yearsToAdd);

        // Create the card
        $card = Card::create([
            'account_id' => $cardRequest->account_id,
            'card_number' => $cardNumber,
            'card_holder_name' => $account->account_name,
            'card_type' => $cardRequest->card_type,
            'card_brand' => $cardRequest->card_brand,
            'expiry_month' => $expiryDate->format('m'),
            'expiry_year' => $expiryDate->format('Y'),
            'cvv' => str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT),
            'status' => 'active',
            'credit_limit' => $cardRequest->card_type === 'credit' ? 5000.00 : null,
            'available_credit' => $cardRequest->card_type === 'credit' ? 5000.00 : null,
            'daily_limit' => 5000.00,
            'contactless_enabled' => true,
            'online_transactions_enabled' => true,
            'international_transactions_enabled' => false,
        ]);

        // Update the request
        $cardRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'created_card_id' => $card->id,
        ]);

        return redirect()->route('admin.card-requests.index')
            ->with('success', "Card request approved. Card number: {$cardNumber}");
    }

    public function reject(Request $request, CardRequest $cardRequest)
    {
        if ($cardRequest->status !== 'pending') {
            return back()->with('error', 'Only pending requests can be rejected.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $cardRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejected_at' => now(),
        ]);

        return redirect()->route('admin.card-requests.index')
            ->with('success', 'Card request rejected successfully.');
    }

    private function generateCardNumber(): string
    {
        // Generate a unique 16-digit card number
        // First 4 digits based on card brand (using generic pattern)
        do {
            $cardNumber = '4' . str_pad(rand(0, 999999999999999), 15, '0', STR_PAD_LEFT);
        } while (Card::where('card_number', $cardNumber)->exists());

        return $cardNumber;
    }
}
