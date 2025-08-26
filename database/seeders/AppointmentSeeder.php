<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;
use App\Models\Appointment;
use Illuminate\Support\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $johnCustomer = User::where('email', 'john.customer@themabinti.com')->first();
        $janeCustomer = User::where('email', 'jane.customer@themabinti.com')->first();
        $basicSeller = User::where('email', 'basic.seller@themabinti.com')->first();
        $standardSeller = User::where('email', 'standard.seller@themabinti.com')->first();
        $premiumSeller = User::where('email', 'premium.seller@themabinti.com')->first();

        // Ensure all required users and services exist
        if (!$johnCustomer || !$janeCustomer || !$basicSeller || !$standardSeller || !$premiumSeller) {
            $this->command->error("Cannot seed appointments: Required users or services not found. Run UserSeeder and ServiceSeeder first.");
            return;
        }

        $manicureService = Service::where('user_id', $basicSeller->id)->where('title', 'Basic Manicure')->first();
        $massageService = Service::where('user_id', $standardSeller->id)->where('title', 'Deep Tissue Massage')->first();
        $wellnessService = Service::where('user_id', $premiumSeller->id)->where('title', 'Holistic Wellness Coaching')->first();

        if (!$manicureService || !$massageService || !$wellnessService) {
            $this->command->error("Cannot seed appointments: Required services not found. Run ServiceSeeder first.");
            return;
        }

        // --- John Customer's Appointments ---
        // Pending appointment with Basic Seller
        Appointment::create([
            'customer_id' => $johnCustomer->id,
            'seller_id' => $basicSeller->id,
            'service_id' => $manicureService->id,
            'appointment_date' => Carbon::now()->addDays(2)->setHour(10)->setMinute(0)->setSecond(0), // 2 days from now, 10:00 AM
            'status' => 'pending',
            'total_amount' => $manicureService->price,
            'notes' => 'Customer prefers clear coat.',
        ]);

        // Confirmed appointment with Standard Seller
        Appointment::create([
            'customer_id' => $johnCustomer->id,
            'seller_id' => $standardSeller->id,
            'service_id' => $massageService->id,
            'appointment_date' => Carbon::now()->addDays(5)->setHour(14)->setMinute(30)->setSecond(0), // 5 days from now, 2:30 PM
            'status' => 'confirmed',
            'total_amount' => $massageService->price,
            'notes' => 'Focus on lower back.',
        ]);

        // Completed appointment with Premium Seller (in the past)
        Appointment::create([
            'customer_id' => $johnCustomer->id,
            'seller_id' => $premiumSeller->id,
            'service_id' => $wellnessService->id,
            'appointment_date' => Carbon::now()->subDays(7)->setHour(9)->setMinute(0)->setSecond(0), // 7 days ago, 9:00 AM
            'status' => 'completed',
            'total_amount' => $wellnessService->price,
            'notes' => 'First session, went well.',
        ]);

        // --- Jane Customer's Appointments ---
        // Cancelled appointment
        Appointment::create([
            'customer_id' => $janeCustomer->id,
            'seller_id' => $basicSeller->id,
            'service_id' => $manicureService->id,
            'appointment_date' => Carbon::now()->addDays(1)->setHour(11)->setMinute(0)->setSecond(0),
            'status' => 'cancelled',
            'total_amount' => $manicureService->price,
            'notes' => 'Customer had a conflict.',
        ]);

        // Another pending appointment
        Appointment::create([
            'customer_id' => $janeCustomer->id,
            'seller_id' => $standardSeller->id,
            'service_id' => $massageService->id,
            'appointment_date' => Carbon::now()->addDays(10)->setHour(16)->setMinute(0)->setSecond(0),
            'status' => 'pending',
            'total_amount' => $massageService->price,
            'notes' => 'Needs to confirm specific location.',
        ]);
    }
}