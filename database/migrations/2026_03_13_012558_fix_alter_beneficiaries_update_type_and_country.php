<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The previous migration (2026_03_13_000449) failed partway through on MySQL
        // because it tried to drop the beneficiaries table while transfers has a FK to it.
        // This migration completes the work using ALTER TABLE instead of drop/recreate.

        // First, clean up any leftover temp table from the failed migration
        Schema::dropIfExists('beneficiaries_temp');

        // Check if beneficiary_type_new column exists (from partial run of previous migration)
        if (Schema::hasColumn('beneficiaries', 'beneficiary_type_new')) {
            // Previous migration partially ran - copy data and drop temp column
            if (!Schema::hasColumn('beneficiaries', 'beneficiary_type')) {
                // beneficiary_type was already dropped, rename new column
                DB::statement("ALTER TABLE beneficiaries CHANGE beneficiary_type_new beneficiary_type ENUM('domestic', 'international') NOT NULL DEFAULT 'domestic'");
            } else {
                // Both columns exist - update and drop the temp one
                DB::statement("UPDATE beneficiaries SET beneficiary_type = beneficiary_type_new");
                Schema::table('beneficiaries', function ($table) {
                    $table->dropColumn('beneficiary_type_new');
                });
                DB::statement("ALTER TABLE beneficiaries MODIFY beneficiary_type ENUM('domestic', 'international') NOT NULL DEFAULT 'domestic'");
            }
        } else {
            // Previous migration didn't run or didn't get to add the temp column
            // Just alter the enum column directly and update values
            DB::statement("ALTER TABLE beneficiaries MODIFY beneficiary_type ENUM('personal', 'business', 'domestic', 'international') NOT NULL DEFAULT 'domestic'");

            DB::table('beneficiaries')
                ->where('beneficiary_type', 'personal')
                ->update(['beneficiary_type' => 'domestic']);

            DB::table('beneficiaries')
                ->where('beneficiary_type', 'business')
                ->update(['beneficiary_type' => 'international']);

            DB::statement("ALTER TABLE beneficiaries MODIFY beneficiary_type ENUM('domestic', 'international') NOT NULL DEFAULT 'domestic'");
        }

        // Alter country column to allow longer values
        DB::statement("ALTER TABLE beneficiaries MODIFY country VARCHAR(100) NOT NULL DEFAULT 'US'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE beneficiaries MODIFY country VARCHAR(100) NOT NULL DEFAULT 'US'");

        DB::statement("ALTER TABLE beneficiaries MODIFY beneficiary_type ENUM('domestic', 'international', 'personal', 'business') NOT NULL DEFAULT 'domestic'");

        DB::table('beneficiaries')
            ->where('beneficiary_type', 'domestic')
            ->update(['beneficiary_type' => 'personal']);

        DB::table('beneficiaries')
            ->where('beneficiary_type', 'international')
            ->update(['beneficiary_type' => 'business']);

        DB::statement("ALTER TABLE beneficiaries MODIFY beneficiary_type ENUM('personal', 'business') NOT NULL DEFAULT 'personal'");

        DB::statement("ALTER TABLE beneficiaries MODIFY country VARCHAR(2) NOT NULL DEFAULT 'US'");
    }
};
