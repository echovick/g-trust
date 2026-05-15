<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

function adminWith(): User
{
    return User::factory()->create(['is_admin' => true]);
}

function holderWith(float $balance = 1000.00): Account
{
    $user = User::factory()->create(['name' => 'Holder One']);
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

it('approving a pending debit deducts the balance and stamps balance_after correctly', function () {
    $admin = adminWith();
    $account = holderWith(1000);

    $txn = Transaction::create([
        'account_id' => $account->id,
        'transaction_type' => 'debit',
        'category' => 'transfer',
        'description' => 'pending debit',
        'amount' => 250,
        'currency' => 'USD',
        'balance_after' => 1000, // stale at submission
        'reference_number' => 'TXN-' . strtoupper(uniqid()),
        'status' => 'pending',
        'transaction_date' => now(),
    ]);

    $response = $this->actingAs($admin)->post("/admin/transactions/{$txn->id}/approve");

    $response->assertStatus(302);
    $response->assertSessionHasNoErrors();

    $txn->refresh();
    $account->refresh();

    expect($txn->status)->toBe('completed')
        ->and((float) $txn->balance_after)->toBe(750.00)
        ->and((float) $account->balance)->toBe(750.00)
        ->and((float) $account->available_balance)->toBe(750.00);
});

it('approving a pending credit grows the balance and stamps the new balance', function () {
    $admin = adminWith();
    $account = holderWith(1000);

    $txn = Transaction::create([
        'account_id' => $account->id,
        'transaction_type' => 'credit',
        'category' => 'deposit',
        'description' => 'pending credit',
        'amount' => 300,
        'currency' => 'USD',
        'balance_after' => 1000,
        'reference_number' => 'TXN-' . strtoupper(uniqid()),
        'status' => 'pending',
        'transaction_date' => now(),
    ]);

    $this->actingAs($admin)->post("/admin/transactions/{$txn->id}/approve")
        ->assertStatus(302)
        ->assertSessionHasNoErrors();

    expect((float) $txn->fresh()->balance_after)->toBe(1300.00)
        ->and((float) $account->fresh()->balance)->toBe(1300.00);
});

it('editing a completed transaction amount recomputes the chain so the last row matches the account', function () {
    $admin = adminWith();
    $account = holderWith(1000);

    $t1 = Transaction::create([
        'account_id' => $account->id,
        'transaction_type' => 'credit',
        'category' => 'deposit',
        'description' => 'old credit',
        'amount' => 100,
        'currency' => 'USD',
        'balance_after' => 1100,
        'reference_number' => 'TXN-' . strtoupper(uniqid()),
        'status' => 'completed',
        'transaction_date' => now()->subDays(2),
    ]);

    $t2 = Transaction::create([
        'account_id' => $account->id,
        'transaction_type' => 'debit',
        'category' => 'transfer',
        'description' => 'old debit',
        'amount' => 200,
        'currency' => 'USD',
        'balance_after' => 900,
        'reference_number' => 'TXN-' . strtoupper(uniqid()),
        'status' => 'completed',
        'transaction_date' => now()->subDay(),
    ]);

    // Move account balance to 900 to reflect the two completed txns.
    $account->update(['balance' => 900, 'available_balance' => 900]);

    // Bump t1 amount from 100 to 150 (a +50 credit adjustment).
    $response = $this->actingAs($admin)->put("/admin/transactions/{$t1->id}", [
        'description' => 'old credit',
        'transaction_date' => $t1->transaction_date->toDateTimeString(),
        'amount' => 150,
    ]);

    $response->assertStatus(302)->assertSessionHasNoErrors();

    $account->refresh();
    $t1->refresh();
    $t2->refresh();

    // Account balance should have moved by +50.
    expect((float) $account->balance)->toBe(950.00);

    // Chain must end at the account balance.
    expect((float) $t2->balance_after)->toBe(950.00);

    // Walking forward: pre-history + t1.delta = t1.balance_after; + t2.delta = t2.balance_after.
    // pre = 950 - (+150) - (-200) = 1000.
    expect((float) $t1->balance_after)->toBe(1150.00);
});

it('rejects approval when account has insufficient balance', function () {
    $admin = adminWith();
    $account = holderWith(50);

    $txn = Transaction::create([
        'account_id' => $account->id,
        'transaction_type' => 'debit',
        'category' => 'transfer',
        'description' => 'too big',
        'amount' => 500,
        'currency' => 'USD',
        'balance_after' => 50,
        'reference_number' => 'TXN-' . strtoupper(uniqid()),
        'status' => 'pending',
        'transaction_date' => now(),
    ]);

    $response = $this->actingAs($admin)->post("/admin/transactions/{$txn->id}/approve");

    // The controller throws inside DB::transaction; Laravel surfaces this as a 500 in tests
    // because there is no error handler converting it. Either way, the transaction must NOT be applied.
    expect($txn->fresh()->status)->toBe('pending')
        ->and((float) $account->fresh()->balance)->toBe(50.00);
});
