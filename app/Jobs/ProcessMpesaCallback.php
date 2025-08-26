<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\User;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Notifications\PaymentStatusNotification;
use App\Notifications\PackageActivatedNotification;

class ProcessMpesaCallback implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Payment $payment;
    protected array $stkCallback;
    protected ?string $merchantRequestId;
    protected int $resultCode;
    protected string $resultDesc;

    public function __construct(Payment $payment, array $stkCallback, ?string $merchantRequestId, int $resultCode, string $resultDesc)
    {
        $this->payment = $payment;
        $this->stkCallback = $stkCallback;
        $this->merchantRequestId = $merchantRequestId;
        $this->resultCode = $resultCode;
        $this->resultDesc = $resultDesc;
    }

    public function handle(): void
    {
        if ($this->payment->status !== 'pending') {
            Log::info("Payment {$this->payment->id} (CheckoutRequestID: {$this->payment->checkout_request_id}) already processed with status '{$this->payment->status}'. Skipping job.");
            return;
        }

        $this->payment->merchant_request_id = $this->merchantRequestId;
        $this->payment->response_data = $this->stkCallback;

        if ($this->resultCode === 0) {
            $this->payment->status = 'completed';
            $callbackMetadata = $this->stkCallback['CallbackMetadata']['Item'] ?? [];
            $mpesaReceiptNumber = null;
            foreach ($callbackMetadata as $item) {
                if ($item['Name'] === 'MpesaReceiptNumber') {
                    $mpesaReceiptNumber = $item['Value'];
                    break;
                }
            }
            $this->payment->mpesa_receipt_number = $mpesaReceiptNumber;

            $user = $this->payment->user;
            if (!$user) {
                Log::error("Payment {$this->payment->id} completed but user not found. Skipping user/package update.");
            } else {
                switch ($this->payment->payment_type) {
                    case 'seller_registration':
                    case 'package_upgrade':
                        // <<< IMPORTANT CHANGES HERE >>>
                        $user->user_type = 'seller'; // Change user to seller type
                        $user->seller_package = $this->payment->package_type;
                        $user->package_expiry_date = Carbon::now()->addMonths(config('themabinti.package_duration_months'));
                        $user->pending_seller_package = null; // Clear pending flag
                        $user->save();
                        $user->notify(new PackageActivatedNotification($user, $this->payment->package_type));
                        Log::info("User {$user->id} package updated to {$this->payment->package_type} and user_type changed to seller.");
                        break;

                    case 'service_payment':
                        $storedData = $this->payment->response_data['service_id'] ?? [];
                        $appointmentId = $storedData['appointment_id'] ?? null;
                        if ($appointmentId) {
                            $appointment = Appointment::find($appointmentId);
                            if ($appointment && $appointment->status === 'pending') {
                                $appointment->status = 'confirmed';
                                $appointment->save();
                                Log::info("Appointment {$appointment->id} confirmed due to payment.");
                            }
                        }
                        break;
                }
            }
            $user?->notify(new PaymentStatusNotification($this->payment));

        } else {
            $this->payment->status = 'failed';
            Log::warning("Payment {$this->payment->id} (CheckoutRequestID: {$this->payment->checkout_request_id}) failed. ResultCode: {$this->resultCode}, Desc: {$this->resultDesc}");
            $this->payment->user?->notify(new PaymentStatusNotification($this->payment));
            // For seller_registration failure: User remains 'customer' with 'pending_seller_package' set.
            // They will need to retry payment.
        }
        $this->payment->save();
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProcessMpesaCallback Job Failed for payment {$this->payment->id}: " . $exception->getMessage());
    }

    /**
     * Handle a job failure.
     */
    // public function failed(\Throwable $exception): void
    // {
    //     Log::error("ProcessMpesaCallback Job Failed for payment {$this->payment->id}: " . $exception->getMessage());
    //     // Optionally, update payment status to 'failed' or 'processing_error' and notify admin
    //     // $this->payment->status = 'processing_error';
    //     // $this->payment->save();
    //     // AdminNotification::dispatch("Mpesa callback processing failed for payment {$this->payment->id}. Error: {$exception->getMessage()}");
    // }
}