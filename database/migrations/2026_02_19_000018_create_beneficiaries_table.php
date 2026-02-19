<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('nickname')->nullable();
            $table->string('account_number');
            $table->string('routing_number')->nullable();
            $table->string('bank_name');
            $table->string('bank_address')->nullable();
            $table->string('swift_code')->nullable(); // For international
            $table->string('iban')->nullable(); // For international
            $table->string('country', 2)->default('US');
            $table->enum('beneficiary_type', ['personal', 'business']);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
