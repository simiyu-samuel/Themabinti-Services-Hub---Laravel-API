<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\MpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Payment\InitiatePaymentRequest; // Import
use App\Jobs\ProcessMpesaCallback; // Import
use Illuminate\Database\Eloquent\ModelNotFoundException; // For findOrFail
use Illuminate\Support\Str; // Import Str facade for unique ID
use App\Services\AdminSettingsService;

class PaymentController extends Controller
{
    protected MpesaService $mpesaService;
    protected AdminSettingsService $adminSettingsService;

    public function __construct(MpesaService $mpesaService, AdminSettingsService $adminSettingsService)
    {
        $this->mpesaService = $mpesaService;
        $this->adminSettingsService = $adminSettingsService;
        
    }

    /**
     * Initiate an M-Pesa STK Push.
     */
    public function initiateStkPush(InitiatePaymentRequest $request)
    {
        $user = $request->user();
        $amount = $request->validated('amount');
        $phone = $request->validated('phone_number');
        $paymentType = $request->validated('payment_type');
        $packageType = $request->validated('package_type');
        $serviceId = $request->validated('service_id');
        $appointmentId = $request->validated('appointment_id');
        
        // --- IMPORTANT: Dynamically get package price for validation ---
        $expectedAmount = 0;
        if (in_array($paymentType, ['seller_registration', 'package_upgrade'])) {
            $expectedAmount = $this->adminSettingsService->get('seller_package_price_' . $packageType);
            // Fallback to config default if admin hasn't set it in DB
            if ($expectedAmount === null) {
                $expectedAmount = config('themabinti.seller_packages.' . $packageType . '.price');
            }
        }
        
        // You might need more complex logic here for service_payment, etc.
        // For simplicity, for service_payment, assume amount is fixed by service price.
        // For actual service_payment, you'd fetch service->price directly.
        if ($paymentType === 'service_payment' && $serviceId) {
             $service = \App\Models\Service::find($serviceId);
             if ($service) $expectedAmount = $service->price;
        }


        // Validation check for amount
        if ($expectedAmount === null || $amount != $expectedAmount) {
            Log::error("Payment amount mismatch for user {$user->id}. Sent: {$amount}, Expected: {$expectedAmount} for type {$paymentType}/{$packageType}");
            return response()->json(['message' => 'Payment amount mismatch. Please verify the package price or service amount.'], 422);
        }


        // --- IMPORTANT CHANGE HERE ---
        // Generate a truly unique internal checkout_request_id from the start.
        // This acts as a unique placeholder before M-Pesa returns its own CheckoutRequestID.
        $temporaryCheckoutId = 'INITIATING_' . Str::uuid(); // Use UUID for strong uniqueness
        $accountReference = 'THEMABINTI_' . strtoupper(str_replace('_', '', $paymentType)) . '_' . Str::random(8);
        $transactionDesc = 'Themabinti Services Hub - ' . ucwords(str_replace('_', ' ', $paymentType));

        // Create a pending payment record with the unique temporary ID.
        // This will succeed without a duplicate entry constraint violation.
        $payment = Payment::create([
            'user_id' => $user->id,
            'checkout_request_id' => $temporaryCheckoutId, // Use the unique temporary ID
            'amount' => $amount,
            'phone_number' => $phone,
            'payment_type' => $paymentType,
            'package_type' => $packageType,
            'status' => 'pending_initiation', // NEW STATUS: Indicates we are awaiting STK push response
            'response_data' => [
                'service_id' => $serviceId,
                'appointment_id' => $appointmentId,
                'account_reference' => $accountReference,
            ],
        ]);

        $result = $this->mpesaService->stkPush($amount, $phone, $accountReference, $transactionDesc);

        if ($result['success']) {
            // If STK push initiation was successful, update the record with the real M-Pesa CheckoutRequestID
            $payment->checkout_request_id = $result['CheckoutRequestID'];
            $payment->status = 'pending'; // Change status to 'pending' as STK push is sent
            $payment->save();

            return response()->json([
                'message' => $result['CustomerMessage'],
                'checkout_request_id' => $result['CheckoutRequestID'],
                'payment_id' => $payment->id,
            ], 200);
        } else {
            // If STK push initiation failed (e.g., network error, invalid phone from Safaricom),
            // update the existing payment record with a 'failed' status and the M-Pesa error details.
            // This record will now have a unique (temporary) checkout_request_id but a 'failed' status.
            $payment->status = 'failed'; // Or 'initiation_failed' if you want more granularity
            $payment->response_data = array_merge($payment->response_data, ['initiation_failure' => $result['data'] ?? $result['message']]);
            $payment->save();

            return response()->json(['message' => $result['message'] ?? 'STK Push initiation failed due to an error.', 'error_data' => $result['data'] ?? null], 500);
        }
    }

    /**
     * Handle M-Pesa STK Push callbacks.
     * This endpoint is called by Safaricom, MUST be publicly accessible and return 200 OK fast.
     */
    public function mpesaCallback(Request $request)
    {
        // Log the raw incoming callback for debugging
        Log::info('M-Pesa Callback Received:', $request->all());

        // Validate and extract necessary data from the callback
        $callbackData = $request->json()->all();

        // Check if the callback structure is as expected for STK Push
        if (!isset($callbackData['Body']['stkCallback'])) {
            Log::warning('Invalid M-Pesa callback structure received.', ['data' => $callbackData]);
            return response()->json(['message' => 'Invalid callback data'], 400);
        }

        $stkCallback = $callbackData['Body']['stkCallback'];
        $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
        // $resultCode = $stkCallback['ResultCode'] ?? -1; // Default to -1 for unknown
        $resultCodeRaw = $stkCallback['ResultCode'] ?? -1;
        $resultCode = is_numeric($resultCodeRaw) ? (int)$resultCodeRaw : -1;
        $resultDesc = $stkCallback['ResultDesc'] ?? 'No description.';
        $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;

        if (!$checkoutRequestId) {
            Log::warning('M-Pesa callback missing CheckoutRequestID.', ['data' => $stkCallback]);
            return response()->json(['message' => 'CheckoutRequestID missing from callback'], 400);
        }

        // Find the corresponding payment record
        try {
            $payment = Payment::where('checkout_request_id', $checkoutRequestId)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            Log::error("M-Pesa callback received for unknown CheckoutRequestID: {$checkoutRequestId}", ['callback_data' => $stkCallback]);
            return response()->json(['message' => 'Payment record not found for this callback'], 404);
        }

        // Dispatch to a queue job for asynchronous and robust processing
        // This is crucial to ensure the M-Pesa API receives a 200 OK response immediately
        // and complex logic doesn't time out the callback.
        ProcessMpesaCallback::dispatch($payment, $stkCallback, $merchantRequestId, $resultCode, $resultDesc);

        // Respond immediately with 200 OK to M-Pesa
        return response()->json(['C2B Command Received' => 'true'], 200);
    }

    /**
     * Check the status of a specific payment using our database record.
     * Optionally, you could query M-Pesa for real-time status if the record is still pending after some time.
     */
    public function getPaymentStatus(string $checkoutRequestId)
    {
        try {
            // Ensure only the owner can query their payment status
            $payment = Payment::where('checkout_request_id', $checkoutRequestId)
                                ->where('user_id', auth()->id()) // Crucial for security
                                ->firstOrFail();

            // Optional: If payment is still 'pending' after a few minutes,
            // you could initiate an STK push query to Safaricom here.
            // This would be for robustness against missed callbacks.
            // if ($payment->status === 'pending' && $payment->created_at->diffInMinutes(now()) > 2) {
            //     $mpesaQueryResult = $this->mpesaService->queryStkStatus($checkoutRequestId);
            //     // Process and update payment status based on mpesaQueryResult if needed
            //     // And then return the updated payment status.
            // }

            return response()->json([
                'status' => $payment->status,
                'payment' => $payment,
                'message' => 'Payment status retrieved successfully.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Payment record not found or you are not authorized to view it.'], 404);
        }
    }

    /**
     * Get authenticated user's payment history.
     */
    public function userPaymentHistory(Request $request)
    {
        $payments = $request->user()->payments()->latest()->paginate(10); // Paginate results

        return response()->json([
            'payments' => $payments,
            'message' => 'Payment history retrieved successfully.'
        ], 200);
    }
}