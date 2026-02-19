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
        Schema::create('bill_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('payee_name');
            $table->string('payee_account_number')->nullable();
            $table->string('payee_type'); // utility, credit_card, loan, rent, etc.
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('reference_number')->unique();
            $table->text('memo')->nullable();
            $table->enum('status', ['pending', 'scheduled', 'processing', 'completed', 'failed'])->default('pending');
            $table->enum('frequency', ['one_time', 'weekly', 'bi_weekly', 'monthly', 'quarterly', 'yearly'])->default('one_time');
            $table->timestamp('scheduled_date');
            $table->timestamp('next_payment_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('auto_pay')->default(false);
            $table->timestamps();

            $table->index(['status']);
            $table->index(['scheduled_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bill_payments');
    }
};
