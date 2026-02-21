<?php

namespace App\Http\Controllers\Admin;

use App\Models\Loan;
use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LoanController extends AdminController
{
    public function index(Request $request)
    {
        $loans = Loan::query()
            ->with(['user', 'linkedAccount'])
            ->when($request->search, function ($query, $search) {
                $query->where('loan_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->type, function ($query, $type) {
                $query->where('loan_type', $type);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_loans' => Loan::count(),
            'active_loans' => Loan::where('status', 'active')->count(),
            'pending_loans' => Loan::where('status', 'pending')->count(),
            'total_outstanding' => Loan::where('status', 'active')->sum('outstanding_balance'),
            'total_disbursed' => Loan::whereIn('status', ['active', 'paid_off'])->sum('loan_amount'),
        ];

        return Inertia::render('admin/loans/Index', [
            'loans' => $loans,
            'stats' => $stats,
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    public function show(Loan $loan)
    {
        $loan->load(['user', 'linkedAccount']);

        // Calculate payment history (mock for now - in real app, you'd have a loan_payments table)
        $paymentSchedule = $this->calculatePaymentSchedule($loan);

        return Inertia::render('admin/loans/Show', [
            'loan' => $loan,
            'payment_schedule' => $paymentSchedule,
        ]);
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id', 'name', 'email']);
        $accounts = Account::with('user')
            ->where('is_active', true)
            ->get();

        return Inertia::render('admin/loans/Create', [
            'users' => $users,
            'accounts' => $accounts,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'loan_type' => ['required', 'in:personal,mortgage,auto,business,student'],
            'loan_amount' => ['required', 'numeric', 'min:100'],
            'interest_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'term_months' => ['required', 'integer', 'min:1', 'max:360'],
            'currency' => ['required', 'string', 'size:3'],
            'linked_account_id' => ['nullable', 'exists:accounts,id'],
            'auto_pay' => ['boolean'],
        ]);

        $loan = DB::transaction(function () use ($validated) {
            // Generate unique loan number
            do {
                $loanNumber = 'LN-' . strtoupper(uniqid());
            } while (Loan::where('loan_number', $loanNumber)->exists());

            // Calculate monthly payment using simple formula (in production, use proper amortization)
            $principal = $validated['loan_amount'];
            $monthlyRate = ($validated['interest_rate'] / 100) / 12;
            $numPayments = $validated['term_months'];

            if ($monthlyRate > 0) {
                $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $numPayments)) / (pow(1 + $monthlyRate, $numPayments) - 1);
            } else {
                $monthlyPayment = $principal / $numPayments;
            }

            return Loan::create([
                'user_id' => $validated['user_id'],
                'loan_number' => $loanNumber,
                'loan_type' => $validated['loan_type'],
                'loan_amount' => $validated['loan_amount'],
                'outstanding_balance' => $validated['loan_amount'],
                'interest_rate' => $validated['interest_rate'],
                'currency' => $validated['currency'],
                'term_months' => $validated['term_months'],
                'remaining_months' => $validated['term_months'],
                'monthly_payment' => round($monthlyPayment, 2),
                'origination_date' => now(),
                'maturity_date' => now()->addMonths($validated['term_months']),
                'next_payment_date' => now()->addMonth()->startOfMonth(),
                'status' => 'pending',
                'total_paid' => 0,
                'auto_pay' => $validated['auto_pay'] ?? false,
                'linked_account_id' => $validated['linked_account_id'],
            ]);
        });

        return redirect()->route('admin.loans.show', $loan)
            ->with('success', 'Loan created successfully. Awaiting approval.');
    }

    public function approve(Request $request, Loan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->withErrors(['status' => 'Only pending loans can be approved.']);
        }

        $validated = $request->validate([
            'disburse_to_account_id' => ['required', 'exists:accounts,id'],
        ]);

        DB::transaction(function () use ($loan, $validated) {
            // Disburse loan amount to account
            $account = Account::findOrFail($validated['disburse_to_account_id']);
            $account->balance += $loan->loan_amount;
            $account->available_balance += $loan->loan_amount;
            $account->save();

            // Create transaction record
            \App\Models\Transaction::create([
                'account_id' => $account->id,
                'transaction_type' => 'credit',
                'category' => 'loan',
                'description' => "Loan disbursement - {$loan->loan_number}",
                'amount' => $loan->loan_amount,
                'currency' => $loan->currency,
                'balance_after' => $account->balance,
                'reference_number' => 'LOAN-' . strtoupper(uniqid()),
                'status' => 'completed',
                'transaction_date' => now(),
            ]);

            // Update loan status
            $loan->update([
                'status' => 'active',
                'linked_account_id' => $validated['disburse_to_account_id'],
            ]);
        });

        return back()->with('success', 'Loan approved and funds disbursed successfully.');
    }

    public function adjustPayment(Request $request, Loan $loan)
    {
        if ($loan->status !== 'active') {
            return back()->withErrors(['status' => 'Only active loans can have payment adjustments.']);
        }

        $validated = $request->validate([
            'new_monthly_payment' => ['required', 'numeric', 'min:0.01'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $loan->update([
            'monthly_payment' => $validated['new_monthly_payment'],
        ]);

        return back()->with('success', 'Loan payment adjusted successfully.');
    }

    public function writeOff(Request $request, Loan $loan)
    {
        if (!in_array($loan->status, ['active', 'defaulted'])) {
            return back()->withErrors(['status' => 'Only active or defaulted loans can be written off.']);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $loan->update([
            'status' => 'defaulted',
            'outstanding_balance' => 0,
        ]);

        return back()->with('success', 'Loan written off successfully.');
    }

    public function toggleAutoPay(Request $request, Loan $loan)
    {
        $loan->update([
            'auto_pay' => !$loan->auto_pay,
        ]);

        $status = $loan->auto_pay ? 'enabled' : 'disabled';
        return back()->with('success', "Auto-pay {$status} successfully.");
    }

    private function calculatePaymentSchedule(Loan $loan)
    {
        // Simple payment schedule calculation (for demonstration)
        $schedule = [];
        $balance = $loan->loan_amount;
        $monthlyRate = ($loan->interest_rate / 100) / 12;
        $payment = $loan->monthly_payment;
        $currentDate = $loan->origination_date;

        for ($i = 1; $i <= min($loan->term_months, 12); $i++) {
            $interestPayment = $balance * $monthlyRate;
            $principalPayment = $payment - $interestPayment;
            $balance -= $principalPayment;

            $schedule[] = [
                'payment_number' => $i,
                'payment_date' => $currentDate->copy()->addMonths($i)->format('Y-m-d'),
                'payment_amount' => round($payment, 2),
                'principal' => round($principalPayment, 2),
                'interest' => round($interestPayment, 2),
                'balance' => round(max(0, $balance), 2),
                'status' => $i <= ($loan->term_months - $loan->remaining_months) ? 'paid' : 'pending',
            ];
        }

        return $schedule;
    }
}
