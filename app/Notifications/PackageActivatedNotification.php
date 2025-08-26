<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PackageActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $user;
    protected string $packageType;

    public function __construct(User $user, string $packageType)
    {
        $this->user = $user;
        $this->packageType = $packageType;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Your Themabinti Seller Package is Active!')
                    ->greeting("Hello {$notifiable->name},")
                    ->line("Congratulations! Your **" . ucfirst($this->packageType) . " Seller Package** is now active on Themabinti Services Hub.")
                    ->line("Your package is valid until: " . $this->user->package_expiry_date->format('F d, Y'))
                    ->line('You can now enjoy all the benefits of your chosen package:')
                    ->line(' - ' . ($this->user->seller_package === 'premium' ? 'Unlimited' : config('themabinti.seller_packages.' . $this->user->seller_package . '.services_limit')) . ' Services')
                    ->line(' - Enhanced listing visibility')
                    ->line(' - Access to your Seller Dashboard for service management and analytics.')
                    ->action('Go to Seller Dashboard', url('/dashboard/seller'))
                    ->line('We are excited to have you as part of our growing community!');
    }
}