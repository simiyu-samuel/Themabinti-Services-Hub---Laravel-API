<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackageExpiryReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $seller;

    public function __construct(User $seller)
    {
        $this->seller = $seller;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Action Required: Your Themabinti Seller Package is Expiring Soon!')
                    ->greeting("Hello {$notifiable->name},")
                    ->line("This is a friendly reminder that your **" . ucfirst($this->seller->seller_package) . " Seller Package** on Themabinti Services Hub is set to expire on **{$this->seller->package_expiry_date->format('F d, Y')}**.")
                    ->line('To ensure uninterrupted service and retain your benefits, please renew your package before this date.')
                    ->action('Renew Your Package Now', url('/dashboard/seller/package-upgrade')) // Placeholder URL
                    ->line('If you have already renewed, please disregard this email.')
                    ->line('Thank you for being a part of Themabinti Services Hub!');
    }
}