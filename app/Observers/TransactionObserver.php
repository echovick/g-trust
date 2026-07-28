<?php

namespace App\Observers;

use App\Mail\TransactionAlertMail;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TransactionObserver
{
    /**
     * Alerts must never be sent for a transaction that is later rolled back,
     * so defer every handler until the surrounding DB transaction commits.
     */
    public bool $afterCommit = true;

    public function created(Transaction $transaction): void
    {
        if ($transaction->status === 'completed') {
            $this->sendAlert($transaction);
        }
    }

    public function updated(Transaction $transaction): void
    {
        // Only alert on the pending/failed -> completed transition, so an edit
        // to an already-completed transaction does not re-alert the customer.
        if ($transaction->wasChanged('status') && $transaction->status === 'completed') {
            $this->sendAlert($transaction);
        }
    }

    /**
     * A failure here must never break the banking operation that triggered it.
     */
    protected function sendAlert(Transaction $transaction): void
    {
        try {
            $transaction->loadMissing('account.user');

            $user = $transaction->account?->user;

            if (! $user?->email) {
                Log::warning('Transaction alert skipped: no recipient email.', [
                    'transaction_id' => $transaction->id,
                    'account_id' => $transaction->account_id,
                ]);

                return;
            }

            // Sent inline: this deployment runs no queue worker, so a queued
            // mailable would sit in the jobs table undelivered.
            Mail::to($user->email)->send(new TransactionAlertMail($transaction, $user));
        } catch (\Throwable $e) {
            Log::error('Transaction alert failed to send.', [
                'transaction_id' => $transaction->id,
                'exception' => $e->getMessage(),
            ]);
        }
    }
}
