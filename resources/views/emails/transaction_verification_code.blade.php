<x-mail::message>
# Transfer Verification Code

Hello {{ $userName }},

You have been issued a one-time transfer verification code to complete your transfer on your G-Trust account.

<x-mail::panel>
**Account:** {{ $accountName }} ({{ $accountNumber }})<br>
**Verification Code:** {{ $code }}<br>
**Expires:** {{ $expiresAt }}
</x-mail::panel>

## How to use this code

Enter the verification code when prompted during your transfer. This code is valid for **24 hours** and can only be used **once**.

<x-mail::button url="{{ config('app.url') }}/dashboard/transfers/create" color="red">
Complete Your Transfer
</x-mail::button>

**Important:** Do not share this code with anyone. G-Trust staff will never ask for this code.

If you did not request this code, please contact our support team immediately.

Thanks,<br>
The {{ config('app.name') }} Team
</x-mail::message>
