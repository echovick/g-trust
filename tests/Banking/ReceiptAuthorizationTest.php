<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;

it('rejects another user trying to download a receipt for a transaction they do not own', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();

    $account = Account::create([
        'user_id' => $owner->id,
        'account_number' => 'ACC0000000099',
        'account_name' => Account::buildAccountName($owner->name, 'checking'),
        'account_type' => 'checking',
        'currency' => 'USD',
        'balance' => 100,
        'available_balance' => 100,
        'is_active' => true,
    ]);

    $txn = Transaction::create([
        'account_id' => $account->id,
        'transaction_type' => 'credit',
        'category' => 'deposit',
        'description' => 'test',
        'amount' => 100,
        'currency' => 'USD',
        'balance_after' => 100,
        'reference_number' => 'RCPT-' . strtoupper(uniqid()),
        'status' => 'completed',
        'transaction_date' => now(),
    ]);

    $this->actingAs($stranger)
        ->get("/dashboard/transactions/{$txn->id}/receipt")
        ->assertForbidden();
});
