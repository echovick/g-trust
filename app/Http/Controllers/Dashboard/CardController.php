<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Card;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CardController extends Controller
{
    public function index(Request $request): Response
    {
        $cards = Card::whereHas('account', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('account')
            ->latest()
            ->get();

        return Inertia::render('dashboard/Cards', [
            'cards' => $cards,
        ]);
    }

    public function updateSettings(Request $request, Card $card)
    {
        // Verify user owns this card through account
        if ($card->account->user_id !== $request->user()->id) {
            abort(403);
        }

        $validated = $request->validate([
            'daily_limit' => ['nullable', 'numeric', 'min:0'],
            'contactless_enabled' => ['boolean'],
            'online_transactions_enabled' => ['boolean'],
            'international_transactions_enabled' => ['boolean'],
        ]);

        $card->update($validated);

        return back()->with('success', 'Card settings updated successfully.');
    }

    public function freeze(Card $card)
    {
        // Verify user owns this card
        if ($card->account->user_id !== auth()->id()) {
            abort(403);
        }

        $card->update(['status' => 'frozen']);

        return back()->with('success', 'Card frozen successfully.');
    }

    public function unfreeze(Card $card)
    {
        // Verify user owns this card
        if ($card->account->user_id !== auth()->id()) {
            abort(403);
        }

        $card->update(['status' => 'active']);

        return back()->with('success', 'Card activated successfully.');
    }

    public function reportLost(Card $card)
    {
        // Verify user owns this card
        if ($card->account->user_id !== auth()->id()) {
            abort(403);
        }

        $card->update(['status' => 'lost']);

        return back()->with('success', 'Card reported as lost. A replacement will be sent.');
    }
}
