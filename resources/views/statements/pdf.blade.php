<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Statement - {{ $account->account_number }}</title>
    <style>
        @media print {
            @page {
                margin: 1cm;
            }
            .no-print {
                display: none;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2563eb;
        }

        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .account-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
        }

        .info-section h3 {
            color: #2563eb;
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .info-label {
            font-weight: 600;
            color: #4b5563;
        }

        .info-value {
            color: #1f2937;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .transactions-table thead {
            background-color: #2563eb;
            color: white;
        }

        .transactions-table th,
        .transactions-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .transactions-table th {
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .transactions-table tbody tr:hover {
            background-color: #f9fafb;
        }

        .debit {
            color: #dc2626;
            font-weight: 600;
        }

        .credit {
            color: #16a34a;
            font-weight: 600;
        }

        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-completed {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #854d0e;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .summary {
            margin-top: 30px;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 8px;
        }

        .summary h3 {
            color: #2563eb;
            margin-bottom: 15px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .summary-item {
            text-align: center;
            padding: 15px;
            background-color: white;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .summary-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 11px;
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .print-button:hover {
            background-color: #1d4ed8;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            color: #d1d5db;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print / Save as PDF</button>

    <div class="header">
        <h1>Account Statement</h1>
        <p>Generated on {{ now()->format('F d, Y \a\t H:i:s') }}</p>
    </div>

    <div class="account-info">
        <div class="info-section">
            <h3>Account Information</h3>
            <div class="info-row">
                <span class="info-label">Account Holder:</span>
                <span class="info-value">{{ $user->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Account Number:</span>
                <span class="info-value">{{ $account->account_number }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Account Type:</span>
                <span class="info-value">{{ ucfirst($account->account_type) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Currency:</span>
                <span class="info-value">{{ $account->currency }}</span>
            </div>
        </div>

        <div class="info-section">
            <h3>Statement Period</h3>
            <div class="info-row">
                <span class="info-label">From:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">To:</span>
                <span class="info-value">{{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Current Balance:</span>
                <span class="info-value">{{ $account->currency }} {{ number_format($account->balance, 2) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Available Balance:</span>
                <span class="info-value">{{ $account->currency }} {{ number_format($account->available_balance, 2) }}</span>
            </div>
        </div>
    </div>

    @php
        $totalDebit = $transactions->where('transaction_type', 'debit')->sum('amount');
        $totalCredit = $transactions->where('transaction_type', 'credit')->sum('amount');
        $transactionCount = $transactions->count();
    @endphp

    <div class="summary">
        <h3>Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Transactions</div>
                <div class="summary-value">{{ $transactionCount }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Debits</div>
                <div class="summary-value debit">{{ $account->currency }} {{ number_format($totalDebit, 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Credits</div>
                <div class="summary-value credit">{{ $account->currency }} {{ number_format($totalCredit, 2) }}</div>
            </div>
        </div>
    </div>

    @if($transactions->count() > 0)
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Reference</th>
                    <th>Category</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Balance</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($transaction->transaction_date)->format('M d, Y') }}</td>
                        <td>{{ $transaction->description }}</td>
                        <td>{{ $transaction->reference_number }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $transaction->category)) }}</td>
                        <td class="debit">
                            @if($transaction->transaction_type === 'debit')
                                {{ $account->currency }} {{ number_format($transaction->amount, 2) }}
                            @endif
                        </td>
                        <td class="credit">
                            @if($transaction->transaction_type === 'credit')
                                {{ $account->currency }} {{ number_format($transaction->amount, 2) }}
                            @endif
                        </td>
                        <td>{{ $account->currency }} {{ number_format($transaction->balance_after, 2) }}</td>
                        <td>
                            <span class="status-badge status-{{ $transaction->status }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p>No transactions found for the selected period.</p>
        </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated statement and does not require a signature.</p>
        <p>For inquiries, please contact customer support.</p>
        <p>&copy; {{ now()->year }} G-Trust Bank. All rights reserved.</p>
    </div>
</body>
</html>
