<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $basicSeller = User::where('email', 'basic.seller@themabinti.com')->first();
        $standardSeller = User::where('email', 'standard.seller@themabinti.com')->first();
        $johnCustomer = User::where('email', 'john.customer@themabinti.com')->first();

        if ($basicSeller) {
            Payment::create([
                'user_id' => $basicSeller->id,
                'checkout_request_id' => 'ws_CO_270720242200000001', // Dummy ID
                'merchant_request_id' => '12345-67890-ABCDE',
                'amount' => 500.00,
                'phone_number' => $basicSeller->phone_number,
                'payment_type' => 'seller_registration',
                'package_type' => 'basic',
                'status' => 'completed',
                'mpesa_receipt_number' => 'NF789X2Y3Z',
                'response_data' => json_encode(['ResultDesc' => 'Payment successful for basic package.']),
                'created_at' => now()->subMonth(), // Payment happened a month ago
                'updated_at' => now()->subMonth(),
            ]);
        }

        if ($standardSeller) {
            Payment::create([
                'user_id' => $standardSeller->id,
                'checkout_request_id' => 'ws_CO_270720242200000002',
                'merchant_request_id' => '54321-09876-UVWXY',
                'amount' => 1500.00,
                'phone_number' => $standardSeller->phone_number,
                'payment_type' => 'package_upgrade',
                'package_type' => 'standard',
                'status' => 'completed',
                'mpesa_receipt_number' => 'PLMNO1234Q',
                'response_data' => json_encode(['ResultDesc' => 'Payment successful for standard package.']),
                'created_at' => now()->subWeeks(2),
                'updated_at' => now()->subWeeks(2),
            ]);
        }

        if ($johnCustomer) {
            // A dummy service payment for John Customer
            $massageService = \App\Models\Service::where('title', 'Deep Tissue Massage')->first();
            if ($massageService) {
                Payment::create([
                    'user_id' => $johnCustomer->id,
                    'checkout_request_id' => 'ws_CO_270720242200000003',
                    'merchant_request_id' => '98765-43210-FGHIJ',
                    'amount' => $massageService->price,
                    'phone_number' => $johnCustomer->phone_number,
                    'payment_type' => 'service_payment',
                    'status' => 'completed',
                    'mpesa_receipt_number' => 'QWERTY5678',
                    'response_data' => json_encode(['ResultDesc' => 'Payment successful for service.']),
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDays(5),
                ]);
            }
        }
    }
}