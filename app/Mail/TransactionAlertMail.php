<?php

namespace App\Mail;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TransactionAlertMail extends Mailable
{
    use SerializesModels;

    public function __construct(
        public Transaction $transaction,
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        $type = $this->transaction->transaction_type === 'credit' ? 'Credit' : 'Debit';
        $amount = number_format($this->transaction->amount, 2);
        $currency = $this->transaction->currency;

        return new Envelope(
            subject: "[G-Trust] {$type} Alert: {$currency} {$amount} on your account",
        );
    }

    public function content(): Content
    {
        $account = $this->transaction->account;

        return new Content(
            markdown: 'emails.transaction_alert',
            with: [
                'userName'        => $this->user->name,
                'transactionType' => $this->transaction->transaction_type,
                'amount'          => number_format($this->transaction->amount, 2),
                'currency'        => $this->transaction->currency,
                'description'     => $this->transaction->description,
                'referenceNumber' => $this->transaction->reference_number,
                'status'          => ucfirst($this->transaction->status),
                'balanceAfter'    => number_format($this->transaction->balance_after, 2),
                'accountNumber'   => $account->account_number,
                'accountName'     => $account->account_name,
                'transactionDate' => $this->transaction->transaction_date->format('M d, Y H:i'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
