<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Notifications\PackageExpiryReminderNotification; // Will create

class PackageRemindExpiry extends Command
{
    protected $signature = 'package:remind-expiry';
    protected $description = 'Sends email reminders to sellers whose packages are expiring soon.';

    public function handle()
    {
        $this->info('Checking for expiring seller packages...');

        // Find sellers whose package expires in 7 days
        $expiryThreshold = Carbon::now()->addDays(7);

        $expiringSellers = User::where('user_type', 'seller')
                                ->whereNotNull('seller_package')
                                ->where('is_active', true)
                                ->where('package_expiry_date', '<=', $expiryThreshold)
                                ->where('package_expiry_date', '>', Carbon::now()) // Ensure not already expired
                                ->get();

        if ($expiringSellers->isEmpty()) {
            $this->info('No seller packages expiring in the next 7 days.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expiringSellers->count()} sellers with expiring packages.");

        foreach ($expiringSellers as $seller) {
            // Ensure we don't spam. Maybe only send if last reminder was more than 24h ago
            // Or store a flag on the user/a log
            $seller->notify(new PackageExpiryReminderNotification($seller));
            $this->line("Sent expiry reminder to: {$seller->email} (Expires: {$seller->package_expiry_date->format('Y-m-d')})");
        }

        $this->info('Package expiry reminders sent successfully!');

        return Command::SUCCESS;
    }
}