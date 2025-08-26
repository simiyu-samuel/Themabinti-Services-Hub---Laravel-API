<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Blog;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::where('user_type', 'admin')->first();

        if (!$adminUser) {
            $this->command->error("Admin user not found. Cannot seed blog posts. Run UserSeeder first.");
            return;
        }

        // Clear existing blog images (optional cleanup)
        Storage::disk('public')->deleteDirectory('blog_images');
        Storage::disk('public')->makeDirectory('blog_images');

        // Published Blog Post 1
        Blog::create([
            'admin_id' => $adminUser->id,
            'title' => 'The Ultimate Guide to Kenyan Beauty Trends in 2024',
            'slug' => Str::slug('The Ultimate Guide to Kenyan Beauty Trends in 2024'),
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
            'featured_image' => 'https://via.placeholder.com/800x450?text=Beauty+Trends',
            'excerpt' => 'Discover the hottest beauty trends sweeping across Kenya this year, from natural hair care to sustainable skincare.',
            'status' => 'published',
            'published_at' => Carbon::now()->subDays(10),
        ]);

        // Published Blog Post 2
        Blog::create([
            'admin_id' => $adminUser->id,
            'title' => 'Men\'s Grooming Essentials: A Modern Kenyan Man\'s Handbook',
            'slug' => Str::slug('Men\'s Grooming Essentials A Modern Kenyan Man\'s Handbook'),
            'content' => 'Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Donec sed odio dui. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Sed posuere consectetur est at lobortis.',
            'featured_image' => 'https://via.placeholder.com/800x450?text=Mens+Grooming',
            'excerpt' => 'From skincare routines to beard care, this guide covers everything a modern Kenyan man needs to look and feel his best.',
            'status' => 'published',
            'published_at' => Carbon::now()->subDays(5),
        ]);

        // Draft Blog Post
        Blog::create([
            'admin_id' => $adminUser->id,
            'title' => 'Upcoming: The Future of Wellness in Kenya',
            'slug' => Str::slug('Upcoming The Future of Wellness in Kenya'),
            'content' => 'Curabitur blandit tempus porttitor. Etiam porta sem malesuada magna mollis euismod. Cras mattis consectetur purus sit amet fermentum.',
            'featured_image' => 'https://via.placeholder.com/800x450?text=Wellness+Future',
            'excerpt' => 'A sneak peek into the evolving landscape of health and wellness services in Kenya, and what to expect.',
            'status' => 'draft',
            'published_at' => null, // Not published yet
        ]);

        // Future Published Post (will not be visible in public index yet)
        Blog::create([
            'admin_id' => $adminUser->id,
            'title' => 'Healthy Living Tips for Busy Professionals',
            'slug' => Str::slug('Healthy Living Tips for Busy Professionals'),
            'content' => 'Maecenas sed diam eget risus varius blandit sit amet non magna. Integer posuere erat a ante venenatis dapibus posuere velit aliquet.',
            'featured_image' => 'https://via.placeholder.com/800x450?text=Healthy+Living',
            'excerpt' => 'Practical advice for maintaining a balanced and healthy lifestyle amidst a demanding career.',
            'status' => 'published',
            'published_at' => Carbon::now()->addDays(2), // Published in the future
        ]);
    }
}