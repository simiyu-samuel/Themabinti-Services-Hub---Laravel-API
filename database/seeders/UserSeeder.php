<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@themabinti.com',
            'phone_number' => '254712345678', // Example Kenyan number
            'user_type' => 'admin',
            'password' => Hash::make('password'), // Use a simple password for testing
            'email_verified_at' => now(),
            // 'phone_verified_at' => now(),
            'is_active' => true,
        ]);

        // Seller Users (with different packages)
        User::create([
            'name' => 'Basic Seller',
            'email' => 'basic.seller@themabinti.com',
            'phone_number' => '254722334455',
            'user_type' => 'seller',
            'seller_package' => 'basic',
            'package_expiry_date' => Carbon::now()->addMonths(1), // Expires in 1 month
            'location' => 'Nairobi CBD',
            'bio' => 'Experienced in basic beauty services for a quick touch-up.',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            // 'phone_verified_at' => now(),
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Standard Seller',
            'email' => 'standard.seller@themabinti.com',
            'phone_number' => '254733445566',
            'user_type' => 'seller',
            'seller_package' => 'standard',
            'package_expiry_date' => Carbon::now()->addMonths(1),
            'location' => 'Westlands, Nairobi',
            'bio' => 'Offering a range of high-quality health and beauty treatments. Mobile services available.',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            // 'phone_verified_at' => now(),
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Premium Seller',
            'email' => 'premium.seller@themabinti.com',
            'phone_number' => '254744556677',
            'user_type' => 'seller',
            'seller_package' => 'premium',
            'package_expiry_date' => Carbon::now()->addMonths(1),
            'location' => 'Karen, Nairobi',
            'bio' => 'Luxury lifestyle services, from wellness coaching to advanced aesthetic treatments. Tailored experiences.',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            // 'phone_verified_at' => now(),
            'is_active' => true,
        ]);

        // Inactive Seller (for testing admin moderation)
        User::create([
            'name' => 'Inactive Seller',
            'email' => 'inactive.seller@themabinti.com',
            'phone_number' => '254755667788',
            'user_type' => 'seller',
            'seller_package' => 'basic',
            'package_expiry_date' => Carbon::now()->addMonths(1),
            'location' => 'Thika',
            'bio' => 'Temporarily unavailable services.',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            // 'phone_verified_at' => now(),
            'is_active' => false,
        ]);

        // Customer Users
        User::create([
            'name' => 'John Customer',
            'email' => 'john.customer@themabinti.com',
            'phone_number' => '254766778899',
            'user_type' => 'customer',
            'location' => 'Kilimani, Nairobi',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            // 'phone_verified_at' => now(),
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Jane Customer',
            'email' => 'jane.customer@themabinti.com',
            'phone_number' => '254777889900',
            'user_type' => 'customer',
            'location' => 'Mombasa',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            // 'phone_verified_at' => now(),
            'is_active' => true,
        ]);

        // Create a few more random customers (using factories for variety)
        User::factory()->count(5)->create([
            'user_type' => 'customer',
            'email_verified_at' => now(),
            // 'phone_verified_at' => now(),
        ]);
    }
}