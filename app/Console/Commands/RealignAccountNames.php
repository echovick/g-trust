<?php

namespace App\Console\Commands;

use App\Models\Account;
use Illuminate\Console\Command;

class RealignAccountNames extends Command
{
    protected $signature = 'accounts:realign-names';

    protected $description = 'Rewrite every account.account_name to match the related user + account_type.';

    public function handle(): int
    {
        $updated = 0;
        $unchanged = 0;

        Account::with('user')->chunkById(200, function ($accounts) use (&$updated, &$unchanged) {
            foreach ($accounts as $account) {
                if (! $account->user) {
                    $this->warn("Skipping account #{$account->id} ({$account->account_number}) — no user.");
                    continue;
                }

                $desired = Account::buildAccountName($account->user->name, $account->account_type);

                if ($account->account_name === $desired) {
                    $unchanged++;
                    continue;
                }

                $this->line("#{$account->id} {$account->account_number}: \"{$account->account_name}\" -> \"{$desired}\"");
                $account->account_name = $desired;
                $account->save();
                $updated++;
            }
        });

        $this->info("Done. Updated: {$updated}. Already aligned: {$unchanged}.");
        return self::SUCCESS;
    }
}
