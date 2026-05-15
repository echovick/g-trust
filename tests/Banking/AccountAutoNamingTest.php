<?php

use App\Models\Account;
use App\Models\User;

it('auto-generates account_name on admin store, ignoring any submitted name', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $holder = User::factory()->create(['name' => 'Alice Holder']);

    $response = $this->actingAs($admin)->post('/admin/accounts', [
        'user_id' => $holder->id,
        'account_type' => 'business',
        'currency' => 'USD',
        'account_name' => 'Whatever User Typed Here',
        'initial_balance' => 0,
        'is_primary' => false,
    ]);

    $response->assertRedirect();

    $account = Account::where('user_id', $holder->id)->first();
    expect($account)->not->toBeNull();
    expect($account->account_name)->toBe('Alice Holder Business Account');
    expect($account->account_type)->toBe('business');
});

it('rewrites account_name on update when account_type changes', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $holder = User::factory()->create(['name' => 'Bob Holder']);

    $account = Account::create([
        'user_id' => $holder->id,
        'account_number' => 'ACC0000000001',
        'account_name' => 'Bob Holder Checking Account',
        'account_type' => 'checking',
        'currency' => 'USD',
        'balance' => 500,
        'available_balance' => 500,
        'is_active' => true,
        'is_primary' => true,
    ]);

    $response = $this->actingAs($admin)->put("/admin/accounts/{$account->id}", [
        'account_type' => 'savings',
        'currency' => 'USD',
        'balance' => 500,
        'available_balance' => 500,
        'is_active' => true,
        'is_primary' => true,
    ]);

    $response->assertRedirect();

    expect($account->fresh()->account_name)->toBe('Bob Holder Savings Account');
});

it('accounts:realign-names rewrites mismatched names and leaves aligned rows alone', function () {
    $user = User::factory()->create(['name' => 'Eve Tester']);

    $bad = Account::create([
        'user_id' => $user->id,
        'account_number' => 'ACC0000000002',
        'account_name' => 'Some Wrong Name',
        'account_type' => 'business',
        'currency' => 'USD',
        'balance' => 0,
        'available_balance' => 0,
        'is_active' => true,
    ]);

    $good = Account::create([
        'user_id' => $user->id,
        'account_number' => 'ACC0000000003',
        'account_name' => 'Eve Tester Savings Account',
        'account_type' => 'savings',
        'currency' => 'USD',
        'balance' => 0,
        'available_balance' => 0,
        'is_active' => true,
    ]);

    $this->artisan('accounts:realign-names')->assertExitCode(0);

    expect($bad->fresh()->account_name)->toBe('Eve Tester Business Account');
    expect($good->fresh()->account_name)->toBe('Eve Tester Savings Account');
});
