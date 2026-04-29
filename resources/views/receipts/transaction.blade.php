<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Receipt – {{ $transaction->reference_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f3f4f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px;
        }
        .toolbar {
            width: 100%;
            max-width: 520px;
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            justify-content: flex-end;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: opacity 0.15s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-primary { background: #dc2626; color: white; }
        .btn-secondary { background: white; color: #374151; border: 1px solid #d1d5db; }
        .receipt {
            width: 100%;
            max-width: 520px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .receipt-header {
            background: linear-gradient(135deg, #1e3a5f 0%, #dc2626 100%);
            color: white;
            padding: 32px 28px 24px;
            text-align: center;
        }
        .bank-name {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .receipt-title {
            font-size: 13px;
            opacity: 0.85;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }
        .amount-block {
            margin-top: 20px;
        }
        .amount-label {
            font-size: 13px;
            opacity: 0.8;
            margin-bottom: 4px;
        }
        .amount-value {
            font-size: 42px;
            font-weight: 800;
            line-height: 1;
        }
        .amount-value.debit { color: #fca5a5; }
        .amount-value.credit { color: #86efac; }
        .status-badge {
            display: inline-block;
            margin-top: 12px;
            padding: 4px 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .status-completed { background: rgba(134,239,172,0.25); color: #86efac; }
        .status-pending   { background: rgba(253,224,71,0.25);  color: #fde047; }
        .status-cancelled { background: rgba(252,165,165,0.25); color: #fca5a5; }
        .receipt-body { padding: 28px; }
        .row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .row:last-child { border-bottom: none; }
        .row-label {
            font-size: 13px;
            color: #6b7280;
            flex-shrink: 0;
            margin-right: 16px;
        }
        .row-value {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            text-align: right;
            word-break: break-all;
        }
        .row-value.mono {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
        }
        .section-divider {
            background: #f9fafb;
            padding: 10px 28px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #9ca3af;
        }
        .receipt-footer {
            padding: 20px 28px;
            background: #f9fafb;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
        }

        @media print {
            body { background: white; padding: 0; }
            .toolbar { display: none; }
            .receipt {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        @if($isAdmin)
            <a href="/admin/transactions" class="btn btn-secondary">← Back to Transactions</a>
        @else
            <a href="/dashboard/transactions" class="btn btn-secondary">← Back to Transactions</a>
        @endif
        <button onclick="window.print()" class="btn btn-primary">⬇ Download / Print PDF</button>
    </div>

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

        <div class="section-divider">Transaction Details</div>
        <div class="receipt-body">
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
                <span class="row-label">Date & Time</span>
                <span class="row-value">{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y H:i') }}</span>
            </div>
        </div>

        <div class="section-divider">Account Information</div>
        <div class="receipt-body">
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
        </div>

        <div class="section-divider">Balance</div>
        <div class="receipt-body">
            <div class="row">
                <span class="row-label">Balance After</span>
                <span class="row-value">{{ $transaction->currency }} {{ number_format($transaction->balance_after, 2) }}</span>
            </div>
        </div>

        <div class="receipt-footer">
            G-Trust Bank &bull; This is an official transaction receipt<br>
            Generated on {{ now()->format('M d, Y H:i') }}
        </div>
    </div>
</body>
</html>
