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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('loan_number')->unique();
            $table->enum('loan_type', ['personal', 'auto', 'mortgage', 'business', 'student']);
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('outstanding_balance', 15, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->string('currency', 3)->default('USD');
            $table->integer('term_months');
            $table->integer('remaining_months');
            $table->decimal('monthly_payment', 15, 2);
            $table->date('origination_date');
            $table->date('maturity_date');
            $table->date('next_payment_date')->nullable();
            $table->enum('status', ['active', 'paid_off', 'defaulted', 'delinquent'])->default('active');
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->boolean('auto_pay')->default(false);
            $table->foreignId('linked_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->timestamps();

            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
