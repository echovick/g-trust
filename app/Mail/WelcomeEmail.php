<?php

namespace App\Mail;

use App\Models\Account;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class WelcomeEmail extends Mailable
{

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public Account $account
    ) {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to G-Trust - Your Account is Ready!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.welcome',
            with: [
                'userName' => $this->user->name,
                'accountNumber' => $this->account->account_number,
                'accountName' => $this->account->account_name,
                'accountType' => ucfirst($this->account->account_type),
                'currency' => $this->account->currency,
                'balance' => number_format($this->account->balance, 2),
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
