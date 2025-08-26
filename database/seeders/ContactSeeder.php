<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contact;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contact::create([
            'name' => 'Test User',
            'email' => 'test.user@example.com',
            'phone' => '254711223344',
            'subject' => 'Inquiry about Mobile Services',
            'message' => 'I would like to know if all services are available as mobile services, or only specific ones. Please clarify.',
            'status' => 'unread',
        ]);

        Contact::create([
            'name' => 'Service Provider Query',
            'email' => 'provider.query@example.com',
            'subject' => 'Question about Premium Package Features',
            'message' => 'Could you provide more details on the "Custom branding options" for the Premium package?',
            'status' => 'read',
        ]);

        Contact::create([
            'name' => 'Resolved Issue',
            'email' => 'resolved.issue@example.com',
            'subject' => 'Problem with appointment rescheduling',
            'message' => 'I had trouble rescheduling an appointment last week. Is this bug fixed?',
            'status' => 'responded',
            'admin_response' => 'Hello, we have investigated your issue and deployed a fix. Please try again and let us know if you encounter any further problems. Thank you for your feedback!',
        ]);
    }
}