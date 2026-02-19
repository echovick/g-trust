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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_type'); // debit, credit, transfer, payment, deposit, fee
            $table->string('category')->nullable(); // Shopping, Dining, Utilities, Income, Transfer, etc.
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->decimal('balance_after', 15, 2);
            $table->string('reference_number')->unique();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
            $table->string('merchant_name')->nullable();
            $table->string('merchant_category')->nullable();
            $table->foreignId('related_account_id')->nullable()->constrained('accounts')->nullOnDelete(); // For transfers
            $table->timestamp('transaction_date');
            $table->timestamps();

            $table->index(['account_id', 'transaction_date']);
            $table->index(['category']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
