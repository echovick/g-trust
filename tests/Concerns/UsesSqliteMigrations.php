<?php

namespace Tests\Concerns;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Boots the test DB by running each migration individually and skipping
 * any file that contains raw MySQL DDL incompatible with SQLite. Use this
 * in place of RefreshDatabase for tests that hit the database, until the
 * problematic migrations are reworked.
 */
trait UsesSqliteMigrations
{
    use DatabaseTransactions;

    /**
     * Migrations known to use MySQL-only DDL (ALTER ... MODIFY / CHANGE ENUM).
     * They can't run on SQLite. Tests here don't depend on what they would alter.
     */
    protected static array $skippedMigrations = [
        '2026_03_13_000449_alter_beneficiaries_update_type_and_country.php',
        '2026_03_13_012558_fix_alter_beneficiaries_update_type_and_country.php',
    ];

    protected static bool $sqliteSchemaBuilt = false;

    protected function setUpTraits(): array
    {
        // Point the test connection at a file-based sqlite DB so the schema
        // persists across test classes within a single process. We do this
        // BEFORE parent::setUpTraits() — which is where DatabaseTransactions
        // opens its connection.
        $this->switchToBankingTestDatabase();

        if (! self::$sqliteSchemaBuilt) {
            $this->runFilteredMigrations();
            self::$sqliteSchemaBuilt = true;
        }

        return parent::setUpTraits();
    }

    protected function switchToBankingTestDatabase(): void
    {
        $path = env('BANKING_TEST_DB', 'database/testing.sqlite');
        $absolute = str_starts_with($path, '/') ? $path : base_path($path);

        if (! file_exists($absolute)) {
            touch($absolute);
        }

        Config::set('database.connections.sqlite.database', $absolute);
        DB::purge('sqlite');
    }

    protected function runFilteredMigrations(): void
    {
        // Truncate the file-based test DB so each process starts clean.
        $absolute = config('database.connections.sqlite.database');
        if ($absolute && file_exists($absolute)) {
            file_put_contents($absolute, '');
            DB::purge('sqlite');
        }

        $files = glob(database_path('migrations/*.php')) ?: [];
        sort($files);

        foreach ($files as $file) {
            $basename = basename($file);
            if (in_array($basename, self::$skippedMigrations, true)) {
                continue;
            }
            Artisan::call('migrate', [
                '--path'     => 'database/migrations/' . $basename,
                '--realpath' => false,
                '--force'    => true,
            ]);
        }
    }
}
