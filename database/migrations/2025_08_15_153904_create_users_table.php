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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone_number')->unique(); // Added
            $table->enum('user_type', ['customer', 'seller', 'admin'])->default('customer'); // Added
            $table->enum('seller_package', ['basic', 'standard', 'premium'])->nullable(); // Added
            $table->timestamp('package_expiry_date')->nullable(); // Added
            $table->string('location')->nullable(); // Added
            $table->text('bio')->nullable(); // Added
            $table->string('profile_image')->nullable(); // Added
            $table->boolean('is_active')->default(true); // Added
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable(); // Added
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};