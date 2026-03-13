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
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('country', 100)->default('US')->change();
        });

        // SQLite doesn't support altering enum columns, so recreate it
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('beneficiary_type_new')->default('domestic');
        });

        DB::table('beneficiaries')->update([
            'beneficiary_type_new' => DB::raw("CASE beneficiary_type WHEN 'personal' THEN 'domestic' WHEN 'business' THEN 'international' ELSE beneficiary_type END"),
        ]);

        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn('beneficiary_type');
        });

        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->renameColumn('beneficiary_type_new', 'beneficiary_type');
        });

        // Re-add the check constraint via raw SQL for SQLite
        DB::statement("CREATE TABLE beneficiaries_temp AS SELECT * FROM beneficiaries");
        Schema::drop('beneficiaries');

        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->string('account_number');
            $table->string('routing_number')->nullable();
            $table->string('bank_name');
            $table->string('bank_address')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('iban')->nullable();
            $table->string('country', 100)->default('US');
            $table->enum('beneficiary_type', ['domestic', 'international']);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        DB::statement("INSERT INTO beneficiaries SELECT * FROM beneficiaries_temp");
        DB::statement("DROP TABLE beneficiaries_temp");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("CREATE TABLE beneficiaries_temp AS SELECT * FROM beneficiaries");
        Schema::drop('beneficiaries');

        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->string('account_number');
            $table->string('routing_number')->nullable();
            $table->string('bank_name');
            $table->string('bank_address')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('iban')->nullable();
            $table->string('country', 2)->default('US');
            $table->enum('beneficiary_type', ['personal', 'business']);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });

        DB::table('beneficiaries_temp')->get()->each(function ($row) {
            $type = $row->beneficiary_type === 'international' ? 'business' : 'personal';
            DB::table('beneficiaries')->insert([
                ...((array) $row),
                'beneficiary_type' => $type,
                'country' => substr($row->country, 0, 2),
            ]);
        });

        DB::statement("DROP TABLE beneficiaries_temp");
    }
};
