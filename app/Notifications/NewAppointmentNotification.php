<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Appointment $appointment;

    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment->load('service', 'customer', 'seller'); // Ensure relationships are loaded
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isCustomer = ($notifiable->id === $this->appointment->customer_id);

        if ($isCustomer) {
            $subject = "Your Appointment Booking for {$this->appointment->service->title} is Pending";
            $greeting = "Hello {$notifiable->name},";
            $line1 = "Your appointment for **{$this->appointment->service->title}** with **{$this->appointment->seller->name}** has been successfully booked and is awaiting confirmation.";
            $line2 = "Date & Time: **{$this->appointment->appointment_date->format('F d, Y H:i A')}**";
            $actionText = 'View My Appointments';
            $actionUrl = '/dashboard/appointments'; // Placeholder URL
        } else { // It's the seller
            $subject = "New Appointment Request for {$this->appointment->service->title}";
            $greeting = "Hello {$notifiable->name},";
            $line1 = "You have a new appointment request for your service **{$this->appointment->service->title}** from **{$this->appointment->customer->name}**.";
            $line2 = "Date & Time: **{$this->appointment->appointment_date->format('F d, Y H:i A')}**";
            $line3 = "Please log in to confirm or decline this appointment.";
            $actionText = 'Manage My Appointments';
            $actionUrl = '/dashboard/seller/appointments'; // Placeholder URL
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line1)
                    ->line($line2)
                    ->line("Service Price: KES {$this->appointment->total_amount}")
                    ->line($isCustomer ? 'We will notify you once the seller confirms.' : $line3)
                    ->action($actionText, url($actionUrl))
                    ->line('Thank you for using Themabinti Services Hub!');
    }
}