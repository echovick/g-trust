<x-mail::message>
# Welcome to G-Trust, {{ $userName }}!

We're thrilled to have you join the G-Trust family. Your account has been successfully created and is ready to use!

## Your Account Details

Here are your default account details:

<x-mail::panel>
**Account Number:** {{ $accountNumber }}<br>
**Account Name:** {{ $accountName }}<br>
**Account Type:** {{ $accountType }}<br>
**Currency:** {{ $currency }}<br>
**Current Balance:** {{ $currency }} {{ $balance }}
</x-mail::panel>

## What's Next?

You can now start using your account! Here's what you can do:
- View your account dashboard
- Make transfers and payments
- Add beneficiaries
- Apply for loans and investments
- Request a debit card

## Need Help?

If you have any questions or need assistance, our support team is here to help. Feel free to reach out to us at any time.

Thank you for choosing G-Trust. We look forward to serving your financial needs!

Best regards,<br>
The {{ config('app.name') }} Team
</x-mail::message>
