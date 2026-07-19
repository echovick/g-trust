<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactMessageMail extends Mailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $subjectLine,
        public string $messageBody,
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Contact Message: '.$this->subjectLine,
            replyTo: [new Address($this->senderEmail, $this->senderName)],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact',
            with: [
                'senderName' => $this->senderName,
                'senderEmail' => $this->senderEmail,
                'subjectLine' => $this->subjectLine,
                'messageBody' => $this->messageBody,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
