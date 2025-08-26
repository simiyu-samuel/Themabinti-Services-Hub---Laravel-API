<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Appointment $appointment;
    protected string $oldStatus;
    protected string $newStatus;

    public function __construct(Appointment $appointment, string $oldStatus, string $newStatus)
    {
        $this->appointment = $appointment->load('service', 'customer', 'seller');
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isCustomer = ($notifiable->id === $this->appointment->customer_id);

        $subject = "Appointment Status Update: {$this->appointment->service->title}";
        $greeting = "Hello {$notifiable->name},";

        $messageLine1 = "Your appointment for **{$this->appointment->service->title}** (with {$this->getOtherPartyName($notifiable)}) has been updated.";
        $messageLine2 = "The status changed from **" . ucfirst($this->oldStatus) . "** to **" . ucfirst($this->newStatus) . "**.";
        $actionText = 'View Appointment Details';
        $actionUrl = ($isCustomer ? '/dashboard/appointments' : '/dashboard/seller/appointments') . '/' . $this->appointment->id;

        $mailMessage = (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($messageLine1)
                    ->line($messageLine2)
                    ->line("Date & Time: **{$this->appointment->appointment_date->format('F d, Y H:i A')}**");

        if ($this->newStatus === 'confirmed') {
            $mailMessage->line('Your appointment is now confirmed! We look forward to seeing you.');
        } elseif ($this->newStatus === 'completed') {
            $mailMessage->line('Your appointment has been successfully completed. We hope you were satisfied!');
        } elseif ($this->newStatus === 'cancelled') {
            $mailMessage->error()
                        ->line('Your appointment has been cancelled.')
                        ->line('Reason (if any): ' . ($this->appointment->notes ?? 'Not specified.'));
        }

        $mailMessage->action($actionText, url($actionUrl))
                    ->line('Thank you for using Themabinti Services Hub!');

        return $mailMessage;
    }

    protected function getOtherPartyName(object $notifiable): string
    {
        return ($notifiable->id === $this->appointment->customer_id)
            ? $this->appointment->seller->name
            : $this->appointment->customer->name;
    }
}