<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction Receipt – {{ $transaction->reference_number }}</title>
    <style>
        @page { size: A4 portrait; margin: 12mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: white;
            color: #111827;
            font-size: 12px;
            line-height: 1.4;
        }
        .receipt { width: 100%; }
        .receipt-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #dc2626 100%);
            color: white;
            padding: 18px 22px;
            text-align: center;
            border-radius: 6px;
        }
        .bank-name {
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .receipt-title {
            font-size: 10px;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 2px;
        }
        .amount-block { margin-top: 14px; }
        .amount-label { font-size: 10px; opacity: 0.85; }
        .amount-value {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.1;
            margin-top: 2px;
        }
        .amount-value.debit { color: #fca5a5; }
        .amount-value.credit { color: #86efac; }
        .status-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 3px 12px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .status-completed { background: rgba(134,239,172,0.25); color: #86efac; }
        .status-pending   { background: rgba(253,224,71,0.25);  color: #fde047; }
        .status-cancelled { background: rgba(252,165,165,0.25); color: #fca5a5; }

        .section-title {
            margin-top: 16px;
            padding: 6px 0 4px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 6px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .row:last-child { border-bottom: none; }
        .row-label {
            font-size: 11px;
            color: #6b7280;
            flex-shrink: 0;
            margin-right: 12px;
        }
        .row-value {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
            text-align: right;
            word-break: break-all;
        }
        .row-value.mono { font-family: 'Courier New', monospace; font-size: 11px; }

        .receipt-footer {
            margin-top: 18px;
            padding-top: 10px;
            text-align: center;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
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

        <div class="section-title">Transaction Details</div>
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

        <div class="section-title">Account Information</div>
        <div class="row">
            <span class="row-label">Account Name</span>
            <span class="row-value">{{ $transaction->account->account_name }}</span>
        </div>
        <div class="row">
            <span class="row-label">Account Number</span>
            <span class="row-value mono">{{ $transaction->account->account_number }}</span>
        </div>
        <div class="row">
            <span class="row-label">Account Holder</span>
            <span class="row-value">{{ $transaction->account->user->name }}</span>
        </div>
        @if($transaction->relatedAccount)
        <div class="row">
            <span class="row-label">
                {{ $transaction->transaction_type === 'debit' ? 'Recipient Account' : 'Sender Account' }}
            </span>
            <span class="row-value mono">{{ $transaction->relatedAccount->account_number }}</span>
        </div>
        @endif

        <div class="section-title">Balance</div>
        <div class="row">
            <span class="row-label">Balance After</span>
            <span class="row-value">{{ $transaction->currency }} {{ number_format($transaction->balance_after, 2) }}</span>
        </div>

        <div class="receipt-footer">
            G-Trust Bank &bull; This is an official transaction receipt<br>
            Generated on {{ now()->format('M d, Y H:i') }}
        </div>
    </div>
</body>
</html>
