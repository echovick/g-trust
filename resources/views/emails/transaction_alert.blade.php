<x-mail::message>
# {{ $transactionType === 'credit' ? '🟢 Money Received' : '🔴 Money Sent' }}

Hello {{ $userName }},

A **{{ strtoupper($transactionType) }}** transaction has been processed on your G-Trust account.

<x-mail::panel>
**Account:** {{ $accountName }} ({{ $accountNumber }})<br>
**Transaction Type:** {{ ucfirst($transactionType) }}<br>
**Amount:** {{ $currency }} {{ $amount }}<br>
**Description:** {{ $description }}<br>
**Status:** {{ $status }}<br>
**Date:** {{ $transactionDate }}<br>
**Reference:** {{ $referenceNumber }}<br>
**Balance After:** {{ $currency }} {{ $balanceAfter }}
</x-mail::panel>

@if($transactionType === 'credit')
Your account has been credited with **{{ $currency }} {{ $amount }}**.
@else
Your account has been debited with **{{ $currency }} {{ $amount }}**.
@endif

If you did not authorise this transaction, please contact our support team immediately.

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
