<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\Storage; // For handling media files

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get our pre-defined sellers
        $basicSeller = User::where('email', 'basic.seller@themabinti.com')->first();
        $standardSeller = User::where('email', 'standard.seller@themabinti.com')->first();
        $premiumSeller = User::where('email', 'premium.seller@themabinti.com')->first();

        // Clear existing service media for fresh seeding (optional, but good for cleanup)
        Storage::disk('public')->deleteDirectory('service_media');
        Storage::disk('public')->makeDirectory('service_media');

        // Services for Basic Seller (limit 3, 2 photos per service)
        if ($basicSeller) {
            $basicSeller->services()->createMany([
                [
                    'title' => 'Basic Manicure',
                    'description' => 'Quick and clean manicure for well-groomed nails.',
                    'category' => 'Beauty',
                    'subcategory' => 'Nails',
                    'price' => 800.00,
                    'duration' => 30, // minutes
                    'location' => 'Nairobi CBD',
                    'is_mobile' => false,
                    'media_files' => $this->getDummyMediaUrls(2, 'image', 'nails'),
                    'is_active' => true,
                ],
                [
                    'title' => 'Simple Haircut (Men)',
                    'description' => 'A classic haircut for men, includes wash and style.',
                    'category' => 'Hair',
                    'subcategory' => 'Men\'s Hair',
                    'price' => 1200.00,
                    'duration' => 45,
                    'location' => 'Nairobi CBD',
                    'is_mobile' => false,
                    'media_files' => $this->getDummyMediaUrls(2, 'image', 'haircut'),
                    'is_active' => true,
                ],
                [
                    'title' => 'Basic Facial',
                    'description' => 'Rejuvenate your skin with our basic cleansing facial.',
                    'category' => 'Beauty',
                    'subcategory' => 'Skin Care',
                    'price' => 1500.00,
                    'duration' => 60,
                    'location' => 'Nairobi CBD',
                    'is_mobile' => true, // Mobile service
                    'media_files' => $this->getDummyMediaUrls(1, 'image', 'facial'),
                    'is_active' => true,
                ],
            ]);
        }

        // Services for Standard Seller (limit 10, 5 photos + 1 video per service)
        if ($standardSeller) {
            $standardSeller->services()->createMany([
                [
                    'title' => 'Deep Tissue Massage',
                    'description' => 'Relax and unwind with a therapeutic deep tissue massage.',
                    'category' => 'Wellness',
                    'subcategory' => 'Massage',
                    'price' => 3500.00,
                    'duration' => 90,
                    'location' => 'Westlands, Nairobi',
                    'is_mobile' => true,
                    'media_files' => array_merge($this->getDummyMediaUrls(4, 'image', 'massage'), $this->getDummyMediaUrls(1, 'video', 'massage')),
                    'is_active' => true,
                ],
                [
                    'title' => 'Gel Pedicure & Nail Art',
                    'description' => 'Long-lasting gel pedicure with custom nail art designs.',
                    'category' => 'Beauty',
                    'subcategory' => 'Nails',
                    'price' => 2500.00,
                    'duration' => 75,
                    'location' => 'Westlands, Nairobi',
                    'is_mobile' => false,
                    'media_files' => $this->getDummyMediaUrls(5, 'image', 'pedicure'),
                    'is_active' => true,
                ],
                [
                    'title' => 'Professional Makeup (Events)',
                    'description' => 'Flawless makeup application for weddings, parties, and special events.',
                    'category' => 'Beauty',
                    'subcategory' => 'Makeup',
                    'price' => 4000.00,
                    'duration' => 120,
                    'location' => 'Westlands, Nairobi',
                    'is_mobile' => true,
                    'media_files' => array_merge($this->getDummyMediaUrls(3, 'image', 'makeup'), $this->getDummyMediaUrls(1, 'video', 'makeup')),
                    'is_active' => true,
                ],
            ]);
        }

        // Services for Premium Seller (unlimited services, unlimited media)
        if ($premiumSeller) {
            $premiumSeller->services()->createMany([
                [
                    'title' => 'Holistic Wellness Coaching',
                    'description' => 'Personalized coaching for mind, body, and spirit balance.',
                    'category' => 'Health',
                    'subcategory' => 'Coaching',
                    'price' => 8000.00,
                    'duration' => 120,
                    'location' => 'Karen, Nairobi',
                    'is_mobile' => true,
                    'media_files' => array_merge($this->getDummyMediaUrls(6, 'image', 'wellness'), $this->getDummyMediaUrls(2, 'video', 'wellness')),
                    'is_active' => true,
                ],
                [
                    'title' => 'Advanced Anti-Aging Facial',
                    'description' => 'State-of-the-art facial treatments to restore youth and vitality.',
                    'category' => 'Beauty',
                    'subcategory' => 'Skin Care',
                    'price' => 7500.00,
                    'duration' => 90,
                    'location' => 'Karen, Nairobi',
                    'is_mobile' => false,
                    'media_files' => $this->getDummyMediaUrls(8, 'image', 'anti-aging'),
                    'is_active' => true,
                ],
                [
                    'title' => 'Personal Fitness Training',
                    'description' => 'Tailored workout plans and one-on-one sessions for optimal results.',
                    'category' => 'Fitness',
                    'subcategory' => 'Personal Training',
                    'price' => 6000.00,
                    'duration' => 60,
                    'location' => 'Karen, Nairobi',
                    'is_mobile' => true,
                    'media_files' => array_merge($this->getDummyMediaUrls(5, 'image', 'fitness'), $this->getDummyMediaUrls(1, 'video', 'fitness')),
                    'is_active' => true,
                ],
                // Add more premium services if desired
            ]);
        }

        // Service from Inactive Seller (for admin approval testing)
        $inactiveSeller = User::where('email', 'inactive.seller@themabinti.com')->first();
        if ($inactiveSeller) {
            $inactiveSeller->services()->create([
                'title' => 'Hair Braiding (Inactive)',
                'description' => 'Professional hair braiding services.',
                'category' => 'Hair',
                'subcategory' => 'Braids',
                'price' => 2000.00,
                'duration' => 180,
                'location' => 'Thika',
                'is_mobile' => false,
                'media_files' => $this->getDummyMediaUrls(2, 'image', 'braiding'),
                'is_active' => false, // This service is inactive, needs admin approval
            ]);
        }
    }

    /**
     * Generates dummy image/video URLs for testing.
     * In a real scenario, you'd have actual dummy files in storage or use a factory with real data.
     * For Postman testing, these are just strings.
     */
    protected function getDummyMediaUrls(int $count, string $type = 'image', string $keyword = 'placeholder'): array
    {
        $urls = [];
        $dummyBase = 'https://via.placeholder.com';
        $dummyVideo = 'https://www.w3schools.com/html/mov_bbb.mp4'; // Public dummy video link

        for ($i = 1; $i <= $count; $i++) {
            if ($type === 'image') {
                $width = 640;
                $height = 480;
                $urls[] = "{$dummyBase}/{$width}x{$height}?text={$keyword}-{$i}";
            } elseif ($type === 'video') {
                $urls[] = $dummyVideo; // Use a generic video link for now
            }
        }
        return $urls;
    }
}