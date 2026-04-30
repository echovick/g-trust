<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Transfer;
use App\Models\Account;
use App\Models\TransactionVerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TransferController extends Controller
{
    public function index(Request $request): Response
    {
        $userAccountIds = $request->user()->accounts()->pluck('id');

        $transfers = Transfer::where(function ($query) use ($userAccountIds) {
                $query->whereIn('from_account_id', $userAccountIds)
                    ->orWhereIn('to_account_id', $userAccountIds);
            })
            ->with(['fromAccount', 'toAccount', 'beneficiary'])
            ->latest('created_at')
            ->paginate(20);

        return Inertia::render('dashboard/Transfers', [
            'transfers' => $transfers,
            'accounts' => $request->user()->accounts,
            'beneficiaries' => $request->user()->beneficiaries,
        ]);
    }

    public function create(Request $request): Response
    {
        $props = [
            'accounts' => $request->user()->accounts()->where('is_active', true)->get(),
            'beneficiaries' => $request->user()->beneficiaries()->where('is_verified', true)->get(),
        ];

        if ($request->has('beneficiary_id')) {
            $props['beneficiary_id'] = (int) $request->query('beneficiary_id');
        }

        return Inertia::render('dashboard/TransferCreate', $props);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_account_id'     => ['required', 'exists:accounts,id'],
            'transfer_type'       => ['required', 'in:internal,external,international'],
            'amount'              => ['required', 'numeric', 'min:0.01'],
            'description'         => ['required', 'string', 'max:255'],
            'beneficiary_id'      => ['required_if:transfer_type,external,international', 'exists:beneficiaries,id'],
            'to_account_id'       => ['nullable', 'required_if:transfer_type,internal', 'exists:accounts,id'],
            'scheduled_date'      => ['nullable', 'date', 'after_or_equal:today'],
            'verification_code'   => ['required', 'string', 'size:6'],
        ]);

        $fromAccount = Account::findOrFail($validated['from_account_id']);

        // Verify user owns the from account
        if ($fromAccount->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized account access.');
        }

        // Validate verification code
        $code = TransactionVerificationCode::where('account_id', $fromAccount->id)
            ->where('code', strtoupper($validated['verification_code']))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$code) {
            return back()->withErrors(['verification_code' => 'Invalid or expired verification code.']);
        }

        // Check sufficient balance
        if ($fromAccount->available_balance < $validated['amount']) {
            return back()->withErrors(['amount' => 'Insufficient balance.']);
        }

        // Process transfer based on type
        if ($validated['transfer_type'] === 'internal') {
            return $this->processInternalTransfer($fromAccount, $validated, $code);
        } else {
            return $this->processExternalTransfer($fromAccount, $validated, $code);
        }
    }

    private function processInternalTransfer(Account $fromAccount, array $data, TransactionVerificationCode $code)
    {
        $toAccount = Account::findOrFail($data['to_account_id']);

        DB::transaction(function () use ($fromAccount, $toAccount, $data, $code) {
            $amount = $data['amount'];
            $referenceNumber = 'TRF-' . strtoupper(uniqid());

            $exchangeRate = 1;
            $convertedAmount = $amount;

            if ($fromAccount->currency !== $toAccount->currency) {
                $exchangeRate = $this->getExchangeRate($fromAccount->currency, $toAccount->currency);
                $convertedAmount = $amount * $exchangeRate;
            }

            Transfer::create([
                'from_account_id'  => $fromAccount->id,
                'to_account_id'    => $toAccount->id,
                'transfer_type'    => 'internal',
                'amount'           => $amount,
                'from_currency'    => $fromAccount->currency,
                'to_currency'      => $toAccount->currency,
                'exchange_rate'    => $exchangeRate,
                'converted_amount' => $convertedAmount,
                'fee'              => 0,
                'reference_number' => $referenceNumber,
                'description'      => $data['description'],
                'status'           => 'pending',
                'completed_at'     => null,
            ]);

            $code->markAsUsed();
        });

        return redirect()->route('dashboard.transfers.index')
            ->with('success', 'Transfer submitted successfully and is pending approval.');
    }

    private function processExternalTransfer(Account $fromAccount, array $data, TransactionVerificationCode $code)
    {
        DB::transaction(function () use ($fromAccount, $data, $code) {
            $amount = $data['amount'];
            $fee = $data['transfer_type'] === 'international' ? $amount * 0.02 : 2.50;
            $totalAmount = $amount + $fee;

            if ($fromAccount->available_balance < $totalAmount) {
                throw new \Exception('Insufficient balance including transfer fee.');
            }

            $referenceNumber = 'TRF-' . strtoupper(uniqid());
            $status = $data['scheduled_date'] ?? null ? 'scheduled' : 'pending';

            Transfer::create([
                'from_account_id'  => $fromAccount->id,
                'beneficiary_id'   => $data['beneficiary_id'],
                'transfer_type'    => $data['transfer_type'],
                'amount'           => $amount,
                'from_currency'    => $fromAccount->currency,
                'to_currency'      => $fromAccount->currency,
                'exchange_rate'    => 1,
                'converted_amount' => $amount,
                'fee'              => $fee,
                'reference_number' => $referenceNumber,
                'description'      => $data['description'],
                'status'           => $status,
                'scheduled_date'   => $data['scheduled_date'] ?? null,
                'completed_at'     => null,
            ]);

            $code->markAsUsed();
        });

        return redirect()->route('dashboard.transfers.index')
            ->with('success', 'Transfer submitted successfully and is pending approval.');
    }

    private function getExchangeRate(string $from, string $to): float
    {
        // Mock exchange rates - in production, fetch from a real API
        $rates = [
            'USD_EUR' => 0.92,
            'USD_GBP' => 0.79,
            'USD_NGN' => 1550.00,
            'EUR_USD' => 1.09,
            'EUR_GBP' => 0.86,
            'EUR_NGN' => 1685.00,
            'GBP_USD' => 1.27,
            'GBP_EUR' => 1.16,
            'GBP_NGN' => 1962.00,
            'NGN_USD' => 0.00065,
            'NGN_EUR' => 0.00059,
            'NGN_GBP' => 0.00051,
        ];

        $key = $from . '_' . $to;
        return $rates[$key] ?? 1;
    }
}
