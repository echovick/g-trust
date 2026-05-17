<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction Receipt – {{ $transaction->reference_number }}</title>
    <style>
        @page { size: 100mm 140mm; margin: 0; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: white;
            color: #111827;
            font-size: 9px;
            line-height: 1.35;
        }
        .receipt { width: 100mm; padding: 4mm; }
        .receipt-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #dc2626 100%);
            color: white;
            padding: 10px 8px;
            text-align: center;
            border-radius: 4px;
        }
        .bank-name {
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .receipt-title {
            font-size: 7px;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1px;
        }
        .amount-block { margin-top: 8px; }
        .amount-label { font-size: 7px; opacity: 0.85; }
        .amount-value {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.1;
            margin-top: 2px;
            word-break: break-all;
        }
        .amount-value.debit { color: #fca5a5; }
        .amount-value.credit { color: #86efac; }
        .status-badge {
            display: inline-block;
            margin-top: 6px;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 7px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        .status-completed { background: rgba(134,239,172,0.25); color: #86efac; }
        .status-pending   { background: rgba(253,224,71,0.25);  color: #fde047; }
        .status-cancelled { background: rgba(252,165,165,0.25); color: #fca5a5; }

        .rows { margin-top: 10px; }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 4px 0;
            border-bottom: 1px dashed #f3f4f6;
            gap: 8px;
        }
        .row:last-child { border-bottom: none; }
        .row-label {
            font-size: 8px;
            color: #6b7280;
            flex-shrink: 0;
        }
        .row-value {
            font-size: 9px;
            font-weight: 600;
            color: #111827;
            text-align: right;
            word-break: break-word;
        }
        .row-value.mono { font-family: 'Courier New', monospace; font-size: 8px; }

        .receipt-footer {
            margin-top: 12px;
            padding-top: 6px;
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
            border-top: 1px dashed #e5e7eb;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="receipt-header">
            <div class="bank-name">G-Trust Bank</div>
            <div class="receipt-title">Transaction Receipt</div>

            <div class="amount-block">
                <div class="amount-label">
                    {{ $transaction->transaction_type === 'credit' ? 'Amount Received' : 'Amount Sent' }}
                </div>
                <div class="amount-value {{ $transaction->transaction_type }}">
                    {{ $transaction->currency }} {{ number_format($transaction->amount, 2) }}
                </div>
                <div>
                    <span class="status-badge status-{{ $transaction->status }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="rows">
        <div class="row">
            <span class="row-label">Reference</span>
            <span class="row-value mono">{{ $transaction->reference_number }}</span>
        </div>
        <div class="row">
            <span class="row-label">Type</span>
            <span class="row-value">{{ ucfirst($transaction->transaction_type) }}</span>
        </div>
        <div class="row">
            <span class="row-label">Category</span>
            <span class="row-value">{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</span>
        </div>
        <div class="row">
            <span class="row-label">Description</span>
            <span class="row-value">{{ $transaction->description }}</span>
        </div>
        <div class="row">
            <span class="row-label">Date &amp; Time</span>
            <span class="row-value">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y H:i') }}</span>
        </div>

        @php
            $beneficiary = $transaction->relatedAccount
                ? [
                    'name'           => optional($transaction->relatedAccount->user)->name ?? $transaction->relatedAccount->account_name,
                    'account_number' => $transaction->relatedAccount->account_number,
                    'bank_name'      => 'G-Trust Bank',
                    'swift_code'     => null,
                    'iban'           => null,
                    'country'        => null,
                ]
                : (optional($transaction->transfer)->beneficiary
                    ? [
                        'name'           => $transaction->transfer->beneficiary->name,
                        'account_number' => $transaction->transfer->beneficiary->account_number,
                        'bank_name'      => $transaction->transfer->beneficiary->bank_name,
                        'swift_code'     => $transaction->transfer->beneficiary->swift_code,
                        'iban'           => $transaction->transfer->beneficiary->iban,
                        'country'        => $transaction->transfer->beneficiary->country,
                    ]
                    : null);
        @endphp

        @if($beneficiary)
        <div class="row">
            <span class="row-label">Beneficiary</span>
            <span class="row-value">{{ $beneficiary['name'] }}</span>
        </div>
        @if($beneficiary['account_number'])
        <div class="row">
            <span class="row-label">Account No.</span>
            <span class="row-value mono">{{ $beneficiary['account_number'] }}</span>
        </div>
        @endif
        @if($beneficiary['bank_name'])
        <div class="row">
            <span class="row-label">Bank</span>
            <span class="row-value">{{ $beneficiary['bank_name'] }}</span>
        </div>
        @endif
        @if($beneficiary['swift_code'])
        <div class="row">
            <span class="row-label">SWIFT</span>
            <span class="row-value mono">{{ $beneficiary['swift_code'] }}</span>
        </div>
        @endif
        @if($beneficiary['iban'])
        <div class="row">
            <span class="row-label">IBAN</span>
            <span class="row-value mono">{{ $beneficiary['iban'] }}</span>
        </div>
        @endif
        @if($beneficiary['country'])
        <div class="row">
            <span class="row-label">Country</span>
            <span class="row-value">{{ $beneficiary['country'] }}</span>
        </div>
        @endif
        @endif
        </div>

        <div class="receipt-footer">
            G-Trust Bank &bull; This is an official transaction receipt<br>
            Generated on {{ now()->format('M d, Y H:i') }}
        </div>
    </div>
</body>
</html>
