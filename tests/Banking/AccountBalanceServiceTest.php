<?php

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AccountBalanceService;

function makeAccount(float $balance = 1000.00): Account
{
    $user = User::factory()->create();

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

function makeTxn(Account $account, string $type, float $amount, float $balanceAfter, string $status = 'completed', ?\Carbon\CarbonInterface $date = null): Transaction
{
    return Transaction::create([
        'account_id' => $account->id,
        'transaction_type' => $type,
        'category' => 'test',
        'description' => 'test',
        'amount' => $amount,
        'currency' => $account->currency,
        'balance_after' => $balanceAfter,
        'reference_number' => 'TEST-' . strtoupper(uniqid()),
        'status' => $status,
        'transaction_date' => $date ?? now(),
    ]);
}

it('applies a signed delta and returns the freshly-saved account', function () {
    $account = makeAccount(500.00);
    $service = app(AccountBalanceService::class);

    $updated = $service->applyDelta($account->id, -120.50);

    expect((float) $updated->balance)->toBe(379.50)
        ->and((float) $updated->available_balance)->toBe(379.50);

    expect((float) $account->fresh()->balance)->toBe(379.50);
});

it('recompute leaves accounts.balance unchanged and ends the chain at that balance', function () {
    $account = makeAccount(1000.00);

    // Three completed transactions with intentionally wrong balance_after values.
    makeTxn($account, 'credit', 200, 999.99, 'completed', now()->subDays(3));
    makeTxn($account, 'debit',  150, 999.99, 'completed', now()->subDays(2));
    makeTxn($account, 'credit', 50,  999.99, 'completed', now()->subDay());

    $service = app(AccountBalanceService::class);
    $result = $service->recomputeBalanceAfter($account->id);

    expect((float) $account->fresh()->balance)->toBe(1000.00);

    $txns = $account->transactions()
        ->where('status', 'completed')
        ->orderBy('transaction_date')
        ->orderBy('id')
        ->get();

    // Newest txn should end at the stored balance.
    expect((float) $txns->last()->balance_after)->toBe(1000.00);

    // Each row's balance_after = next-older balance_after + that-newer txn's signed delta.
    // Walking forward, balance_after should equal cumulative running sum starting from
    // (account.balance - sum of all completed deltas).
    $sum = (float) $txns->sum(fn ($t) => $t->transaction_type === 'credit' ? (float) $t->amount : -(float) $t->amount);
    $pre = 1000.00 - $sum;
    $running = $pre;
    foreach ($txns as $t) {
        $running += $t->transaction_type === 'credit' ? (float) $t->amount : -(float) $t->amount;
        expect((float) $t->balance_after)->toBe(round($running, 2));
    }

    expect($result['total'])->toBe(3)
        ->and($result['updated'])->toBe(3);
});

it('recompute resets available_balance to balance minus pending debits', function () {
    $account = makeAccount(1000.00);

    makeTxn($account, 'credit', 100, 1000.00, 'completed');
    makeTxn($account, 'debit', 75, 0, 'pending');   // 75 reserved
    makeTxn($account, 'debit', 25, 0, 'pending');   // 25 reserved
    makeTxn($account, 'credit', 999, 0, 'pending'); // pending credit must NOT affect available

    app(AccountBalanceService::class)->recomputeBalanceAfter($account->id);

    $fresh = $account->fresh();
    expect((float) $fresh->balance)->toBe(1000.00)
        ->and((float) $fresh->available_balance)->toBe(900.00); // 1000 - 75 - 25
});

it('recompute is idempotent', function () {
    $account = makeAccount(1000.00);
    makeTxn($account, 'credit', 200, 0, 'completed', now()->subDay());
    makeTxn($account, 'debit', 50, 0, 'completed', now());

    $service = app(AccountBalanceService::class);
    $service->recomputeBalanceAfter($account->id);

    $snapshot = $account->transactions()->orderBy('id')->pluck('balance_after')->all();

    $second = $service->recomputeBalanceAfter($account->id);

    expect($second['updated'])->toBe(0);
    expect($account->transactions()->orderBy('id')->pluck('balance_after')->all())->toEqual($snapshot);
});
