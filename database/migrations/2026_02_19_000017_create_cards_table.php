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
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->string('card_number')->unique(); // Masked: ****1234
            $table->string('card_holder_name');
            $table->enum('card_type', ['debit', 'credit']);
            $table->enum('card_brand', ['visa', 'mastercard', 'amex', 'discover']);
            $table->string('expiry_month', 2);
            $table->string('expiry_year', 4);
            $table->string('cvv')->nullable(); // Encrypted
            $table->enum('status', ['active', 'blocked', 'expired', 'lost', 'stolen'])->default('active');
            $table->decimal('credit_limit', 15, 2)->nullable(); // For credit cards
            $table->decimal('available_credit', 15, 2)->nullable();
            $table->decimal('daily_limit', 15, 2)->default(5000);
            $table->boolean('contactless_enabled')->default(true);
            $table->boolean('online_transactions_enabled')->default(true);
            $table->boolean('international_transactions_enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
