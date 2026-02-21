<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BeneficiaryController extends Controller
{
    public function index(Request $request): Response
    {
        $beneficiaries = $request->user()
            ->beneficiaries()
            ->latest()
            ->paginate(20);

        return Inertia::render('dashboard/Beneficiaries', [
            'beneficiaries' => $beneficiaries,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/BeneficiaryCreate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'routing_number' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_address' => ['nullable', 'string', 'max:500'],
            'swift_code' => ['nullable', 'string', 'max:20'],
            'iban' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:100'],
            'beneficiary_type' => ['required', 'in:domestic,international'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $request->user()->beneficiaries()->create([
            ...$validated,
            'is_verified' => false, // Admin needs to verify
        ]);

        return redirect()->route('dashboard.beneficiaries.index')
            ->with('success', 'Beneficiary added successfully. Pending verification.');
    }

    public function edit(Beneficiary $beneficiary): Response
    {
        // Ensure user owns this beneficiary
        if ($beneficiary->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('dashboard/BeneficiaryEdit', [
            'beneficiary' => $beneficiary,
        ]);
    }

    public function update(Request $request, Beneficiary $beneficiary)
    {
        // Ensure user owns this beneficiary
        if ($beneficiary->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50'],
            'routing_number' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['required', 'string', 'max:255'],
            'bank_address' => ['nullable', 'string', 'max:500'],
            'swift_code' => ['nullable', 'string', 'max:20'],
            'iban' => ['nullable', 'string', 'max:50'],
            'country' => ['required', 'string', 'max:100'],
            'beneficiary_type' => ['required', 'in:domestic,international'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $beneficiary->update([
            ...$validated,
            'is_verified' => false, // Re-verification needed after edit
        ]);

        return redirect()->route('dashboard.beneficiaries.index')
            ->with('success', 'Beneficiary updated successfully. Pending re-verification.');
    }

    public function destroy(Beneficiary $beneficiary)
    {
        // Ensure user owns this beneficiary
        if ($beneficiary->user_id !== auth()->id()) {
            abort(403);
        }

        $beneficiary->delete();

        return redirect()->route('dashboard.beneficiaries.index')
            ->with('success', 'Beneficiary deleted successfully.');
    }
}
