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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Foreign key to users
            $table->string('checkout_request_id')->unique(); // For M-Pesa STK Push
            $table->string('merchant_request_id')->nullable(); // From M-Pesa Callback
            $table->decimal('amount', 10, 2);
            $table->string('phone_number');
            $table->enum('payment_type', ['seller_registration', 'package_upgrade', 'service_payment']);
            $table->enum('package_type', ['basic', 'standard', 'premium'])->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('mpesa_receipt_number')->nullable();
            $table->json('response_data')->nullable(); // Store full M-Pesa response
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};