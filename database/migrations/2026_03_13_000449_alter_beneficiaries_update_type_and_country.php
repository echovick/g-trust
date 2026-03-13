<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // No-op: this migration's logic has been moved to
        // 2026_03_13_012558_fix_alter_beneficiaries_update_type_and_country
        // because the original approach dropped the table, which fails on MySQL
        // due to the transfers_beneficiary_id_foreign FK constraint.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: see up() comment.
    }
};
