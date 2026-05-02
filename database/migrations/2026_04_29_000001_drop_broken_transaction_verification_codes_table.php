<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The 2026_04_30_000001 migration partially executed on first deploy (table created,
        // index step failed), leaving the table in the DB without a migrations record.
        // Dropping here lets the next migration recreate it cleanly.
        Schema::dropIfExists('transaction_verification_codes');
    }

    public function down(): void
    {
        //
    }
};
