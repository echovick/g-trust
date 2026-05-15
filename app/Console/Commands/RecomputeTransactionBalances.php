<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Services\AccountBalanceService;
use Illuminate\Console\Command;

class RecomputeTransactionBalances extends Command
{
    protected $signature = 'transactions:recompute-balances
                            {--log= : Optional file path to also write progress to (survives Forge UI output capture)}
                            {--with-locks : Wrap each account in DB::transaction with row locks (slower; use when other writers may be active)}';

    protected $description = 'Rewrite transactions.balance_after for every account so each chain ends at the current accounts.balance.';

    public function handle(AccountBalanceService $balances): int
    {
        $logPath = $this->option('log');
        $useLocks = (bool) $this->option('with-locks');

        $logFh = null;
        if ($logPath) {
            $logFh = fopen($logPath, 'a');
            if (! $logFh) {
                $this->error("Could not open log file: {$logPath}");
                return self::FAILURE;
            }
        }

        $write = function (string $line, string $level = 'line') use ($logFh) {
            match ($level) {
                'warn'  => $this->warn($line),
                'info'  => $this->info($line),
                'error' => $this->error($line),
                default => $this->line($line),
            };
            if ($logFh) {
                fwrite($logFh, '[' . date('c') . "] {$line}\n");
                fflush($logFh);
            }
            // Force stdout out of PHP's buffer so Forge has something to capture
            // even if the process is killed mid-run.
            @ob_flush();
            @flush();
        };

        $warnings = 0;
        $totalAccounts = 0;
        $totalUpdated = 0;

        $write('Starting transactions:recompute-balances. with-locks=' . ($useLocks ? 'yes' : 'no'), 'info');

        Account::orderBy('id')->chunkById(100, function ($accounts) use ($balances, $write, $useLocks, &$warnings, &$totalAccounts, &$totalUpdated) {
            foreach ($accounts as $account) {
                try {
                    $result = $balances->recomputeBalanceAfter($account->id, $useLocks);
                } catch (\Throwable $e) {
                    $write(sprintf('#%d %s — FAILED: %s', $account->id, $account->account_number, $e->getMessage()), 'error');
                    $warnings++;
                    continue;
                }

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
                    $write($line . '  <- WARNING: negative pre-history balance', 'warn');
                    $warnings++;
                } else {
                    $write($line);
                }
            }
        });

        $write("Done. Accounts processed: {$totalAccounts}. Transaction rows updated: {$totalUpdated}. Warnings: {$warnings}.", 'info');

        if ($logFh) {
            fclose($logFh);
        }

        return self::SUCCESS;
    }
}
