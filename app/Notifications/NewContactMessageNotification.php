<?php

namespace App\Notifications;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewContactMessageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Contact $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Contact Message Received on Themabinti Services Hub')
                    ->greeting('Hello Admin,')
                    ->line('You have received a new contact message:')
                    ->line("Name: **{$this->contact->name}**")
                    ->line("Email: **{$this->contact->email}**")
                    ->line("Phone: **{$this->contact->phone}**")
                    ->line("Subject: **{$this->contact->subject}**")
                    ->line('Message:')
                    ->line("```plain\n{$this->contact->message}\n```") // Format message as code block for readability
                    ->action('View & Respond to Message', url('/admin/contacts/' . $this->contact->id))
                    ->line('Please review and respond promptly.');
    }
}