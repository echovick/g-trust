<?php

namespace App\Http\Controllers\Admin;

use App\Models\Card;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CardController extends AdminController
{
    public function index(Request $request)
    {
        $cards = Card::query()
            ->with(['account.user'])
            ->when($request->search, function ($query, $search) {
                $query->where('card_number', 'like', "%{$search}%")
                    ->orWhere('card_holder_name', 'like', "%{$search}%")
                    ->orWhereHas('account.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('card_type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_cards' => Card::count(),
            'active_cards' => Card::where('status', 'active')->count(),
            'frozen_cards' => Card::where('status', 'frozen')->count(),
            'blocked_cards' => Card::whereIn('status', ['blocked', 'lost'])->count(),
        ];

        return Inertia::render('admin/cards/Index', [
            'cards' => $cards,
            'stats' => $stats,
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    public function show(Card $card)
    {
        $card->load(['account.user']);

        // Get recent transactions for this card (if you have card_id in transactions table)
        // For now, we'll get account transactions
        $recentTransactions = $card->account->transactions()
            ->latest('transaction_date')
            ->limit(10)
            ->get();

        return Inertia::render('admin/cards/Show', [
            'card' => $card,
            'recent_transactions' => $recentTransactions,
        ]);
    }

    public function freeze(Request $request, Card $card)
    {
        if ($card->status !== 'active') {
            return back()->withErrors(['status' => 'Only active cards can be frozen.']);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $card->update([
            'status' => 'frozen',
        ]);

        // Log admin action (we'll implement in Priority 3)

        return back()->with('success', 'Card frozen successfully.');
    }

    public function unfreeze(Request $request, Card $card)
    {
        if ($card->status !== 'frozen') {
            return back()->withErrors(['status' => 'Only frozen cards can be unfrozen.']);
        }

        $card->update([
            'status' => 'active',
        ]);

        return back()->with('success', 'Card unfrozen successfully.');
    }

    public function block(Request $request, Card $card)
    {
        if (in_array($card->status, ['blocked', 'lost'])) {
            return back()->withErrors(['status' => 'Card is already blocked.']);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $card->update([
            'status' => 'blocked',
        ]);

        return back()->with('success', 'Card blocked successfully. A replacement card can be issued.');
    }

    public function reportLost(Request $request, Card $card)
    {
        if ($card->status === 'lost') {
            return back()->withErrors(['status' => 'Card is already reported as lost.']);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $card->update([
            'status' => 'lost',
        ]);

        // In a real app, you'd initiate replacement card issuance here

        return back()->with('success', 'Card reported as lost. Replacement card process initiated.');
    }

    public function updateSettings(Request $request, Card $card)
    {
        $validated = $request->validate([
            'daily_limit' => ['nullable', 'numeric', 'min:0'],
            'contactless_enabled' => ['boolean'],
            'online_transactions_enabled' => ['boolean'],
            'international_transactions_enabled' => ['boolean'],
        ]);

        $card->update($validated);

        return back()->with('success', 'Card settings updated successfully.');
    }

    public function updateLimits(Request $request, Card $card)
    {
        $validated = $request->validate([
            'daily_limit' => ['required', 'numeric', 'min:0'],
        ]);

        $card->update([
            'daily_limit' => $validated['daily_limit'],
        ]);

        return back()->with('success', 'Card limits updated successfully.');
    }

    public function toggleFeature(Request $request, Card $card)
    {
        $validated = $request->validate([
            'feature' => ['required', 'in:contactless_enabled,online_transactions_enabled,international_transactions_enabled'],
        ]);

        $feature = $validated['feature'];
        $card->update([
            $feature => !$card->$feature,
        ]);

        $featureName = str_replace('_', ' ', str_replace('_enabled', '', $feature));
        $status = $card->$feature ? 'enabled' : 'disabled';

        return back()->with('success', ucfirst($featureName) . " {$status} successfully.");
    }
}
