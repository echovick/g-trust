<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;

it('transactions:recompute-balances rewrites mismatched balance_after rows across accounts', function () {
    $user = User::factory()->create();

    $a = Account::create([
        'user_id' => $user->id,
        'account_number' => 'ACC0000000010',
        'account_name' => Account::buildAccountName($user->name, 'checking'),
        'account_type' => 'checking',
        'currency' => 'USD',
        'balance' => 500,
        'available_balance' => 500,
        'is_active' => true,
    ]);

    $b = Account::create([
        'user_id' => $user->id,
        'account_number' => 'ACC0000000011',
        'account_name' => Account::buildAccountName($user->name, 'savings'),
        'account_type' => 'savings',
        'currency' => 'USD',
        'balance' => 250,
        'available_balance' => 250,
        'is_active' => true,
    ]);

    // Account A: balance=500. Two completed: +300 then -100. Pre-history must be 300.
    Transaction::create([
        'account_id' => $a->id, 'transaction_type' => 'credit', 'category' => 't', 'description' => 't',
        'amount' => 300, 'currency' => 'USD', 'balance_after' => 0,
        'reference_number' => 'A1', 'status' => 'completed', 'transaction_date' => now()->subDay(),
    ]);
    Transaction::create([
        'account_id' => $a->id, 'transaction_type' => 'debit', 'category' => 't', 'description' => 't',
        'amount' => 100, 'currency' => 'USD', 'balance_after' => 0,
        'reference_number' => 'A2', 'status' => 'completed', 'transaction_date' => now(),
    ]);

    // Account B: balance=250. One completed: -50. Pre-history must be 300.
    Transaction::create([
        'account_id' => $b->id, 'transaction_type' => 'debit', 'category' => 't', 'description' => 't',
        'amount' => 50, 'currency' => 'USD', 'balance_after' => 0,
        'reference_number' => 'B1', 'status' => 'completed', 'transaction_date' => now(),
    ]);

    $this->artisan('transactions:recompute-balances')->assertExitCode(0);

    // Stored balances unchanged.
    expect((float) $a->fresh()->balance)->toBe(500.00);
    expect((float) $b->fresh()->balance)->toBe(250.00);

    // Last completed row balance_after matches stored balance.
    $aLast = $a->transactions()->where('status', 'completed')->orderByDesc('transaction_date')->orderByDesc('id')->first();
    expect((float) $aLast->balance_after)->toBe(500.00);

    $bLast = $b->transactions()->where('status', 'completed')->orderByDesc('transaction_date')->orderByDesc('id')->first();
    expect((float) $bLast->balance_after)->toBe(250.00);

    // Older row on A: 500 - (-100) = 600.
    $aFirst = $a->transactions()->where('status', 'completed')->orderBy('transaction_date')->orderBy('id')->first();
    expect((float) $aFirst->balance_after)->toBe(600.00);
});
