<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class AccountBalanceService
{
    /**
     * Apply a signed delta to an account's balance under a row lock.
     * Returns the locked, freshly-saved account so the caller can stamp
     * balance_after from $account->balance.
     */
    public function applyDelta(int $accountId, float $delta): Account
    {
        $account = Account::lockForUpdate()->findOrFail($accountId);
        $account->balance = (float) $account->balance + $delta;
        $account->available_balance = (float) $account->available_balance + $delta;
        $account->save();

        return $account;
    }

    /**
     * Rewrite balance_after for every completed transaction on this account,
     * anchored at the current account.balance (treated as authoritative).
     *
     * Walks newest -> oldest: the most recent transaction's balance_after
     * must equal the stored balance; each older row's balance_after is
     * (next-newer balance_after) minus the next-newer transaction's signed
     * delta. account.balance itself is never overwritten.
     *
     * Also resets available_balance to account.balance minus pending debits.
     *
     * @param  bool  $useLocks  Pass false for one-shot backfills where no
     *                         other writer is racing — avoids loading every
     *                         transaction at once and skips row locks.
     * @return array{updated:int, oldest_pre_balance:float, total:int}
     */
    public function recomputeBalanceAfter(int $accountId, bool $useLocks = true): array
    {
        $work = function () use ($accountId, $useLocks) {
            $account = $useLocks
                ? Account::lockForUpdate()->findOrFail($accountId)
                : Account::findOrFail($accountId);

            $running = (float) $account->balance;
            $updated = 0;
            $total = 0;

            $query = Transaction::where('account_id', $accountId)
                ->where('status', 'completed')
                ->orderByDesc('transaction_date')
                ->orderByDesc('id');

            if ($useLocks) {
                $query->lockForUpdate();
            }

            // chunk(...) when unlocked so we don't pull every row into memory.
            // get() under lock keeps the row-lock semantics for the runtime path.
            if ($useLocks) {
                foreach ($query->get() as $txn) {
                    [$running, $updated, $total] = $this->applyRow($txn, $running, $updated, $total);
                }
            } else {
                $query->chunk(500, function ($chunk) use (&$running, &$updated, &$total) {
                    foreach ($chunk as $txn) {
                        [$running, $updated, $total] = $this->applyRow($txn, $running, $updated, $total);
                    }
                });
            }

            $pendingDebits = (float) Transaction::where('account_id', $accountId)
                ->where('status', 'pending')
                ->where('transaction_type', 'debit')
                ->sum('amount');

            $account->available_balance = (float) $account->balance - $pendingDebits;
            $account->save();

            return [
                'updated' => $updated,
                'oldest_pre_balance' => $running,
                'total' => $total,
            ];
        };

        return $useLocks ? DB::transaction($work) : $work();
    }

    /**
     * @return array{0:float,1:int,2:int}
     */
    private function applyRow(Transaction $txn, float $running, int $updated, int $total): array
    {
        $total++;
        if ((float) $txn->balance_after !== $running) {
            $txn->balance_after = $running;
            $txn->save();
            $updated++;
        }
        $delta = $txn->transaction_type === 'credit'
            ? (float) $txn->amount
            : -(float) $txn->amount;

        return [$running - $delta, $updated, $total];
    }
}
