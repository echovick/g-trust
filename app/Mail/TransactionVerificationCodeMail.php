<?php

namespace App\Mail;

use App\Models\Account;
use App\Models\TransactionVerificationCode;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionVerificationCodeMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public TransactionVerificationCode $verificationCode,
        public Account $account,
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[G-Trust] Your Transfer Verification Code',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.transaction_verification_code',
            with: [
                'userName'      => $this->user->name,
                'code'          => $this->verificationCode->code,
                'accountName'   => $this->account->account_name,
                'accountNumber' => $this->account->account_number,
                'expiresAt'     => $this->verificationCode->expires_at->format('M d, Y H:i'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
