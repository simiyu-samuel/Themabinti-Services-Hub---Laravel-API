<?php

namespace App\Notifications;

use App\Models\GeneralBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewGeneralBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected GeneralBooking $booking;

    public function __construct(GeneralBooking $booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // --- FIX FOR OLDER PHP COMPATIBILITY ---
        // Replace $this->booking->message ?? 'No message provided.'
        // with compatible syntax like (isset($this->booking->message) ? $this->booking->message : 'No message provided.')
        // This is necessary because ?? operator requires PHP 7.0+
        $messageContent = isset($this->booking->message) && $this->booking->message !== null
                          ? $this->booking->message
                          : 'No message provided.';
        // --- END FIX ---

        return (new MailMessage)
                    ->subject('New General Appointment Request Received')
                    ->greeting('Hello Admin,')
                    ->line('A new general appointment request has been submitted:')
                    ->line("Name: **{$this->booking->customer_name}**")
                    ->line("Phone: **{$this->booking->customer_phone}**")
                    ->line("Email: **{$this->booking->customer_email}**")
                    ->line("Preferred Date & Time: **{$this->booking->preferred_date_time->format('F d, Y H:i A')}**")
                    ->line('Message:')
                    ->line("```plain\n{$messageContent}\n```") // Use the compatible variable here
                    ->action('View Request in Admin Panel', url('/admin/general-bookings/' . $this->booking->id)) // Admin route
                    ->line('Please review and assign a suitable seller.');
    }
}