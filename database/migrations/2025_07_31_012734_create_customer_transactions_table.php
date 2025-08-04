<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->bigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('order');
            $table->enum('type', ['payment', 'refund', 'credit', 'debit']);
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('CAD');
            $table->string('payment_method')->nullable(); // 'credit_card', 'paypal', etc.
            $table->string('transaction_id')->nullable(); // External payment ID
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Store additional payment data
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['customer_id', 'type']);
            $table->index(['customer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_transactions');
    }
};