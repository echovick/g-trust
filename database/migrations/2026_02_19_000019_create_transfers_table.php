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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_account_id')->constrained('accounts');
            $table->foreignId('to_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('transfer_type', ['internal', 'external', 'wire', 'international']);
            $table->decimal('amount', 15, 2);
            $table->string('from_currency', 3)->default('USD');
            $table->string('to_currency', 3)->default('USD');
            $table->decimal('exchange_rate', 10, 6)->nullable();
            $table->decimal('converted_amount', 15, 2)->nullable();
            $table->decimal('fee', 10, 2)->default(0);
            $table->string('reference_number')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['transfer_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
