<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $status = ucfirst($this->payment->status);
        $paymentId = $this->getPaymentReference();
        $subject = "Themabinti Payment {$status}: #{$paymentId}";
        
        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name},")
            ->line($this->getStatusLine());

        return $this->addStatusSpecificContent($mailMessage)
            ->action('View Payment Details', $this->getPaymentUrl());
    }

    /**
     * Get the payment reference number.
     * PHP 5.3+ compatible for ternary operator.
     */
    protected function getPaymentReference(): string
    {
        // OLD: return $this->payment->mpesa_receipt_number ?? $this->payment->checkout_request_id ?? $this->payment->id;
        // NEW (PHP 5.3+ compatible):
        return (isset($this->payment->mpesa_receipt_number) && $this->payment->mpesa_receipt_number !== null)
            ? $this->payment->mpesa_receipt_number
            : ((isset($this->payment->checkout_request_id) && $this->payment->checkout_request_id !== null)
                ? $this->payment->checkout_request_id
                : $this->payment->id);
    }

    /**
     * Get the main status line for the email.
     */
    protected function getStatusLine(): string
    {
        // Ensuring number_format is robust if amount could be null (though schema says decimal 10,2 NOT NULL)
        $amountFormatted = number_format($this->payment->amount, 2); 
        $paymentTypeFormatted = $this->formatPaymentType();
        $statusUcFirst = ucfirst($this->payment->status);

        return "Your payment of KES {$amountFormatted} for {$paymentTypeFormatted} has been {$statusUcFirst}.";
    }

    /**
     * Add status-specific content to the mail message.
     */
    protected function addStatusSpecificContent(MailMessage $mailMessage): MailMessage
    {
        switch ($this->payment->status) {
            case 'completed':
                return $this->addCompletedContent($mailMessage);
            case 'failed':
                return $this->addFailedContent($mailMessage);
            case 'pending':
                return $this->addPendingContent($mailMessage);
            case 'cancelled':
                return $this->addCancelledContent($mailMessage);
            default:
                return $mailMessage;
        }
    }

    /**
     * Add content for completed payments.
     */
    protected function addCompletedContent(MailMessage $mailMessage): MailMessage
    {
        $mailMessage->success()
            ->line("M-Pesa Receipt Number: {$this->payment->mpesa_receipt_number}")
            ->line('Thank you for your payment! Your transaction has been processed successfully.');

        // Add service-specific information
        if ($this->payment->payment_type === 'seller_registration') {
            $mailMessage->line('Your seller account is now active. You can start listing your products.');
        } elseif ($this->payment->payment_type === 'package_upgrade') {
            // Need to make sure package_type is available for ucfirst
            $packageType = (isset($this->payment->package_type) && $this->payment->package_type !== null) 
                            ? $this->payment->package_type : 'your new';
            $mailMessage->line("Your account has been upgraded to " . ucfirst($packageType) . " package.");
        }

        return $mailMessage;
    }

    /**
     * Add content for failed payments.
     */
    protected function addFailedContent(MailMessage $mailMessage): MailMessage
    {
        $reason = $this->getFailureReason();
        
        return $mailMessage->error()
            ->line('Unfortunately, your payment was not successful.')
            ->line("Reason: {$reason}")
            ->line('Please try again or contact our support team if you continue to experience issues.')
            ->action('Try Again', $this->getRetryUrl());
    }

    /**
     * Add content for pending payments.
     */
    protected function addPendingContent(MailMessage $mailMessage): MailMessage
    {
        return $mailMessage
            ->line('Your payment is currently being processed.')
            ->line('You will receive another notification once the payment is confirmed.')
            ->line('This usually takes a few minutes.');
    }

    /**
     * Add content for cancelled payments.
     */
    protected function addCancelledContent(MailMessage $mailMessage): MailMessage
    {
        return $mailMessage
            ->line('Your payment has been cancelled.')
            ->line('If this was not intentional, please try making the payment again.');
    }

    /**
     * Get the failure reason from payment data.
     */
    protected function getFailureReason(): string
    {
        // OLD: if (isset($this->payment->response_data['ResultDesc'])) { return $this->payment->response_data['ResultDesc']; }
        // NEW (PHP 5.3+ compatible):
        if (isset($this->payment->response_data) && is_array($this->payment->response_data)) {
            if (isset($this->payment->response_data['ResultDesc'])) {
                return $this->payment->response_data['ResultDesc'];
            }
            if (isset($this->payment->response_data['errorMessage'])) { // M-Pesa can use errorMessage too
                return $this->payment->response_data['errorMessage'];
            }
        }
        return 'Payment could not be processed. Please try again.';
    }

    /**
     * Format the payment type for display.
     * OLD: return match ($this->payment->payment_type) { ... }
     * NEW (PHP 5.3+ compatible switch statement):
     */
    protected function formatPaymentType(): string
    {
        $packageTypeFormatted = '';
        if (isset($this->payment->package_type) && $this->payment->package_type !== null) {
            $packageTypeFormatted = ' to ' . ucfirst($this->payment->package_type);
        }

        switch ($this->payment->payment_type) {
            case 'seller_registration':
                return 'Seller Registration';
            case 'package_upgrade':
                return 'Package Upgrade' . $packageTypeFormatted;
            case 'service_payment':
                return 'Service Payment';
            case 'product_listing':
                return 'Product Listing Fee';
            case 'commission_payment':
                return 'Commission Payment';
            default:
                return 'Payment';
        }
    }

    /**
     * Get the payment details URL.
     */
    protected function getPaymentUrl(): string
    {
        return url("/dashboard/payments/{$this->payment->id}");
    }

    /**
     * Get the retry payment URL for failed payments.
     * Note: This assumes a retry endpoint exists or that failed payments can be retried via a new initiation.
     */
    protected function getRetryUrl(): string
    {
        // For current flow, retry usually means going back to package upgrade page
        if ($this->payment->payment_type === 'seller_registration' || $this->payment->payment_type === 'package_upgrade') {
            return url('/dashboard/seller/package-upgrade');
        }
        // Fallback to viewing payment details
        return url("/dashboard/payments/{$this->payment->id}");
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payment_id' => $this->payment->id,
            'status' => $this->payment->status,
            'amount' => $this->payment->amount,
            'payment_type' => $this->payment->payment_type,
            'reference' => $this->getPaymentReference(),
        ];
    }
}