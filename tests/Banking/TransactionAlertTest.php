<?php

use App\Mail\TransactionAlertMail;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

function alertAccount(float $balance = 1000.00): Account
{
    $user = User::factory()->create(['name' => 'Alert Holder']);

    return Account::create([
        'user_id' => $user->id,
        'account_number' => 'ACC' . str_pad((string) random_int(1, 9999999999), 10, '0', STR_PAD_LEFT),
        'account_name' => Account::buildAccountName($user->name, 'checking'),
        'account_type' => 'checking',
        'currency' => 'USD',
        'balance' => $balance,
        'available_balance' => $balance,
        'is_active' => true,
    ]);
}

function makeAlertTxn(Account $account, array $overrides = []): Transaction
{
    return Transaction::create(array_merge([
        'account_id' => $account->id,
        'transaction_type' => 'credit',
        'category' => 'deposit',
        'description' => 'test txn',
        'amount' => 100,
        'currency' => 'USD',
        'balance_after' => $account->balance,
        'reference_number' => 'TXN-' . strtoupper(uniqid()),
        'status' => 'completed',
        'transaction_date' => now(),
    ], $overrides));
}

it('alerts the account holder whenever a completed transaction is created', function () {
    $account = alertAccount();

    makeAlertTxn($account, ['transaction_type' => 'debit', 'category' => 'bill_payment']);

    Mail::assertSent(TransactionAlertMail::class, fn ($mail) => $mail->hasTo($account->user->email));
});

it('does not alert while a transaction is still pending', function () {
    $account = alertAccount();

    makeAlertTxn($account, ['status' => 'pending']);

    Mail::assertNothingSent();
});

it('alerts once the transaction transitions to completed', function () {
    $account = alertAccount();
    $txn = makeAlertTxn($account, ['status' => 'pending']);

    $txn->update(['status' => 'completed']);

    Mail::assertSent(TransactionAlertMail::class, 1);
});

it('does not re-alert when an already completed transaction is edited', function () {
    $account = alertAccount();
    $txn = makeAlertTxn($account);

    $txn->update(['description' => 'amended description']);

    Mail::assertSent(TransactionAlertMail::class, 1);
});

it('does not alert for a transaction whose surrounding db transaction rolls back', function () {
    $account = alertAccount();

    try {
        DB::transaction(function () use ($account) {
            makeAlertTxn($account);

            throw new RuntimeException('rollback');
        });
    } catch (RuntimeException) {
        // expected
    }

    Mail::assertNothingSent();
});
