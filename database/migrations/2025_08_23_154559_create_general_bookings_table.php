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
        Schema::create('general_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email');
            $table->datetime('preferred_date_time'); // The date/time slot requested
            $table->text('message')->nullable(); // User's message/details for the booking
            $table->text('admin_notes')->nullable(); // For internal admin notes
            $table->enum('status', ['pending', 'assigned', 'rejected', 'completed'])->default('pending'); // Admin-managed status
            $table->foreignId('assigned_seller_id')->nullable()->constrained('users')->onDelete('set null'); // Optional: Which seller it was assigned to
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_bookings');
    }
};