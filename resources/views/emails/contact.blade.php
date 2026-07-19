<x-mail::message>
# New Contact Message

You have received a new message from the G-Trust contact form.

<x-mail::panel>
**Name:** {{ $senderName }}<br>
**Email:** {{ $senderEmail }}<br>
**Subject:** {{ $subjectLine }}
</x-mail::panel>

## Message

{{ $messageBody }}

<x-mail::button :url="'mailto:'.$senderEmail">
Reply to {{ $senderName }}
</x-mail::button>

You can also simply reply to this email to respond directly to the sender.

Best regards,<br>
The {{ config('app.name') }} Team
</x-mail::message>
