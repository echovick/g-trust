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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('account_number')->unique();
            $table->string('account_name');
            $table->enum('investment_type', ['stocks', 'bonds', 'mutual_funds', 'etf', 'retirement_401k', 'ira']);
            $table->decimal('initial_investment', 15, 2);
            $table->decimal('current_value', 15, 2);
            $table->decimal('total_returns', 15, 2)->default(0);
            $table->decimal('return_percentage', 8, 4)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('opening_date');
            $table->boolean('is_active')->default(true);
            $table->json('holdings')->nullable(); // Store investment breakdown
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
