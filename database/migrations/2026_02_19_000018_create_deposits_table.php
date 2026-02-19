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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->cascadeOnDelete();
            $table->enum('deposit_type', ['check', 'cash', 'wire', 'direct_deposit']);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('reference_number')->unique();
            $table->string('check_number')->nullable();
            $table->string('check_front_image')->nullable(); // Path to uploaded image
            $table->string('check_back_image')->nullable();
            $table->text('memo')->nullable();
            $table->enum('status', ['pending', 'processing', 'approved', 'rejected', 'completed'])->default('pending');
            $table->timestamp('deposit_date');
            $table->timestamp('available_date')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['deposit_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
