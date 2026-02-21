<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BillPayment;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BillPaymentController extends Controller
{
    public function index(Request $request): Response
    {
        $billPayments = BillPayment::whereHas('account', function ($query) use ($request) {
                $query->where('user_id', $request->user()->id);
            })
            ->with('account')
            ->latest()
            ->paginate(20);

        return Inertia::render('dashboard/BillPayments', [
            'billPayments' => $billPayments,
            'accounts' => $request->user()->accounts()->where('is_active', true)->get(),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('dashboard/BillPaymentCreate', [
            'accounts' => $request->user()->accounts()->where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'payee_name' => ['required', 'string', 'max:255'],
            'payee_account_number' => ['nullable', 'string', 'max:50'],
            'payee_type' => ['required', 'in:utility,credit_card,loan,insurance,other'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'memo' => ['nullable', 'string', 'max:500'],
            'frequency' => ['required', 'in:once,weekly,monthly,quarterly,annually'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'auto_pay' => ['boolean'],
        ]);

        $account = Account::findOrFail($validated['account_id']);

        // Verify user owns the account
        if ($account->user_id !== $request->user()->id) {
            abort(403);
        }

        // Check sufficient balance
        if ($account->available_balance < $validated['amount']) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        DB::transaction(function () use ($account, $validated) {
            $referenceNumber = 'BP-' . strtoupper(uniqid());

            // Debit account
            $account->balance -= $validated['amount'];
            $account->available_balance -= $validated['amount'];
            $account->save();

            // Create transaction
            Transaction::create([
                'account_id' => $account->id,
                'transaction_type' => 'debit',
                'category' => 'bill_payment',
                'description' => 'Bill Payment - ' . $validated['payee_name'],
                'amount' => $validated['amount'],
                'currency' => $account->currency,
                'balance_after' => $account->balance,
                'reference_number' => $referenceNumber,
                'status' => 'completed',
                'merchant_name' => $validated['payee_name'],
                'transaction_date' => $validated['scheduled_date'],
            ]);

            // Create bill payment
            $nextPaymentDate = $validated['frequency'] !== 'once'
                ? $this->calculateNextPaymentDate($validated['scheduled_date'], $validated['frequency'])
                : null;

            BillPayment::create([
                'account_id' => $account->id,
                'payee_name' => $validated['payee_name'],
                'payee_account_number' => $validated['payee_account_number'],
                'payee_type' => $validated['payee_type'],
                'amount' => $validated['amount'],
                'currency' => $account->currency,
                'reference_number' => $referenceNumber,
                'memo' => $validated['memo'],
                'status' => 'completed',
                'frequency' => $validated['frequency'],
                'scheduled_date' => $validated['scheduled_date'],
                'next_payment_date' => $nextPaymentDate,
                'completed_at' => now(),
                'auto_pay' => $validated['auto_pay'] ?? false,
            ]);
        });

        return redirect()->route('dashboard.bill-payments.index')
            ->with('success', 'Bill payment completed successfully.');
    }

    public function destroy(BillPayment $billPayment)
    {
        // Verify user owns this bill payment through account
        if ($billPayment->account->user_id !== auth()->id()) {
            abort(403);
        }

        // Can only delete scheduled/recurring payments, not completed ones
        if ($billPayment->status === 'completed' && $billPayment->frequency === 'once') {
            return back()->withErrors(['error' => 'Cannot delete completed one-time payment.']);
        }

        $billPayment->delete();

        return redirect()->route('dashboard.bill-payments.index')
            ->with('success', 'Bill payment deleted successfully.');
    }

    private function calculateNextPaymentDate($currentDate, $frequency): string
    {
        $date = new \DateTime($currentDate);

        switch ($frequency) {
            case 'weekly':
                $date->modify('+1 week');
                break;
            case 'monthly':
                $date->modify('+1 month');
                break;
            case 'quarterly':
                $date->modify('+3 months');
                break;
            case 'annually':
                $date->modify('+1 year');
                break;
        }

        return $date->format('Y-m-d');
    }
}
