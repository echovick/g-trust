<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LoanController extends Controller
{
    public function index(Request $request): Response
    {
        $loans = $request->user()
            ->loans()
            ->with('linkedAccount')
            ->latest()
            ->get();

        return Inertia::render('dashboard/Loans', [
            'loans' => $loans,
        ]);
    }

    public function show(Loan $loan): Response
    {
        // Verify user owns this loan
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        $loan->load('linkedAccount');

        return Inertia::render('dashboard/LoanDetails', [
            'loan' => $loan,
        ]);
    }

    public function toggleAutoPay(Loan $loan)
    {
        // Verify user owns this loan
        if ($loan->user_id !== auth()->id()) {
            abort(403);
        }

        $loan->update([
            'auto_pay' => !$loan->auto_pay,
        ]);

        return back()->with('success', $loan->auto_pay ? 'Auto-pay enabled.' : 'Auto-pay disabled.');
    }
}
