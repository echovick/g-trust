<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\AccountBalanceService;
use Illuminate\Console\Command;

class RecomputeTransactionBalances extends Command
{
    protected $signature = 'transactions:recompute-balances';

    protected $description = 'Rewrite transactions.balance_after for every account so each chain ends at the current accounts.balance.';

    public function handle(AccountBalanceService $balances): int
    {
        $warnings = 0;
        $totalAccounts = 0;
        $totalUpdated = 0;

        Account::orderBy('id')->chunkById(100, function ($accounts) use ($balances, &$warnings, &$totalAccounts, &$totalUpdated) {
            foreach ($accounts as $account) {
                $result = $balances->recomputeBalanceAfter($account->id);
                $totalAccounts++;
                $totalUpdated += $result['updated'];

                $line = sprintf(
                    '#%d %s — txns:%d updated:%d oldest_pre_balance:%.2f',
                    $account->id,
                    $account->account_number,
                    $result['total'],
                    $result['updated'],
                    $result['oldest_pre_balance']
                );

                if ($result['oldest_pre_balance'] < 0) {
                    $this->warn($line . '  <- WARNING: negative pre-history balance');
                    $warnings++;
                } else {
                    $this->line($line);
                }
            }
        });

        $this->info("Done. Accounts processed: {$totalAccounts}. Transaction rows updated: {$totalUpdated}. Warnings: {$warnings}.");
        return self::SUCCESS;
    }
}
