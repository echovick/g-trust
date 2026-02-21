<?php

namespace App\Http\Controllers\Admin;

use App\Models\BillPayment;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class BillPaymentController extends AdminController
{
    public function index(Request $request)
    {
        $billPayments = BillPayment::query()
            ->with(['account.user'])
            ->when($request->search, function ($query, $search) {
                $query->where('payee_name', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('account.user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->frequency, function ($query, $frequency) {
                $query->where('frequency', $frequency);
            })
            ->when($request->payee_type, function ($query, $payeeType) {
                $query->where('payee_type', $payeeType);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total_bill_payments' => BillPayment::count(),
            'pending_payments' => BillPayment::where('status', 'pending')->count(),
            'auto_pay_enabled' => BillPayment::where('auto_pay', true)->count(),
            'total_scheduled_amount' => BillPayment::where('status', 'pending')
                ->whereDate('scheduled_date', '>=', today())
                ->sum('amount'),
        ];

        return Inertia::render('admin/bill-payments/Index', [
            'bill_payments' => $billPayments,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'frequency', 'payee_type']),
        ]);
    }

    public function show(BillPayment $billPayment)
    {
        $billPayment->load(['account.user']);

        // Get payment history (if this is a recurring payment)
        $paymentHistory = [];
        if ($billPayment->frequency !== 'once') {
            // In a real app, you'd fetch actual payment history from a bill_payment_history table
            $paymentHistory = $this->getMockPaymentHistory($billPayment);
        }

        return Inertia::render('admin/bill-payments/Show', [
            'bill_payment' => $billPayment,
            'payment_history' => $paymentHistory,
        ]);
    }

    public function process(Request $request, BillPayment $billPayment)
    {
        if ($billPayment->status !== 'pending') {
            return back()->withErrors(['status' => 'Only pending bill payments can be processed.']);
        }

        $account = $billPayment->account;

        // Check sufficient balance
        if ($account->available_balance < $billPayment->amount) {
            return back()->withErrors(['amount' => 'Insufficient balance in account.']);
        }

        DB::transaction(function () use ($billPayment, $account) {
            // Deduct from account
            $account->balance -= $billPayment->amount;
            $account->available_balance -= $billPayment->amount;
            $account->save();

            // Create transaction record
            Transaction::create([
                'account_id' => $account->id,
                'transaction_type' => 'debit',
                'category' => 'bill_payment',
                'description' => "Bill Payment to {$billPayment->payee_name}",
                'amount' => $billPayment->amount,
                'currency' => $billPayment->currency,
                'balance_after' => $account->balance,
                'reference_number' => $billPayment->reference_number,
                'status' => 'completed',
                'merchant_name' => $billPayment->payee_name,
                'transaction_date' => now(),
            ]);

            // Update bill payment
            $updateData = [
                'status' => 'completed',
                'completed_at' => now(),
            ];

            // If recurring, schedule next payment
            if ($billPayment->frequency !== 'once') {
                $updateData['next_payment_date'] = $this->calculateNextPaymentDate(
                    $billPayment->scheduled_date,
                    $billPayment->frequency
                );
                $updateData['status'] = 'pending'; // Keep as pending for next occurrence
                $updateData['scheduled_date'] = $updateData['next_payment_date'];
            }

            $billPayment->update($updateData);
        });

        return back()->with('success', 'Bill payment processed successfully.');
    }

    public function cancel(Request $request, BillPayment $billPayment)
    {
        if (!in_array($billPayment->status, ['pending'])) {
            return back()->withErrors(['status' => 'Only pending bill payments can be cancelled.']);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $billPayment->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Bill payment cancelled successfully.');
    }

    public function toggleAutoPay(Request $request, BillPayment $billPayment)
    {
        $billPayment->update([
            'auto_pay' => !$billPayment->auto_pay,
        ]);

        $status = $billPayment->auto_pay ? 'enabled' : 'disabled';
        return back()->with('success', "Auto-pay {$status} successfully.");
    }

    private function calculateNextPaymentDate($currentDate, $frequency)
    {
        $date = is_string($currentDate) ? \Carbon\Carbon::parse($currentDate) : $currentDate;

        switch ($frequency) {
            case 'weekly':
                return $date->addWeek();
            case 'monthly':
                return $date->addMonth();
            case 'quarterly':
                return $date->addMonths(3);
            case 'annually':
                return $date->addYear();
            default:
                return $date;
        }
    }

    private function getMockPaymentHistory(BillPayment $billPayment)
    {
        // Mock payment history - in production, fetch from actual payment history table
        $history = [];
        $date = now()->subMonths(6);

        for ($i = 0; $i < 6; $i++) {
            $history[] = [
                'payment_date' => $date->copy()->addMonths($i)->format('Y-m-d'),
                'amount' => $billPayment->amount,
                'status' => 'completed',
                'reference' => 'REF-' . strtoupper(uniqid()),
            ];
        }

        return $history;
    }
}
