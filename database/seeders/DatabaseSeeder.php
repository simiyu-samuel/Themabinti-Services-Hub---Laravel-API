<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call([
            UserSeeder::class, // Users must exist first
            ServiceSeeder::class, // Services need users
            AppointmentSeeder::class, // Appointments need users and services
            PaymentSeeder::class, // Payments need users (and optionally services/appointments)
            ContactSeeder::class,
            BlogSeeder::class, // Blogs need admins
            DefaultAdminSettingsSeeder::class,
        ]);
    }
}
