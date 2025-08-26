<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache; // For OTP storage
use App\Http\Requests\Auth\PhoneVerificationRequest; // Import
use App\Http\Requests\Auth\PhoneVerificationCheckRequest; // Import

class VerificationController extends Controller
{
    public function __construct()
    {
        // Apply throttle to prevent abuse of verification links/resends
         $this->middleware('throttle:verification')->only('verify', 'resend', 'requestPhoneVerification', 'checkPhoneVerification');
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id); // Use findOrFail for 404 if user not found

        if (! URL::hasValidSignature($request)) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 403);
        }

        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            throw new AuthorizationException('This verification link is invalid.');
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        // Consider logging in the user here if this is the end of an email flow from a client
        // Auth::login($user); // Example, depending on your frontend's needs

        return response()->json(['message' => 'Email verified successfully!'], 200);
    }

    public function resend(Request $request)
    {
        $request->validate(['email' => ['required', 'email', 'exists:users,email']]);
        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification link sent to your email!'], 200);
    }

    public function requestPhoneVerification(PhoneVerificationRequest $request) // Use Form Request
    {
        $user = $request->user();

        if ($user->phone_verified_at) {
            return response()->json(['message' => 'Phone number already verified.'], 200);
        }

        // --- PROFESSIONAL SMS OTP IMPLEMENTATION (placeholder) ---
        $otp = random_int(100000, 999999); // Generate a 6-digit OTP
        $otpKey = 'otp_' . $user->id . '_' . $user->phone_number;
        Cache::put($otpKey, $otp, now()->addMinutes(5)); // Store OTP for 5 minutes

        // In a real application, integrate with an SMS gateway here:
        // try {
        //     $message = "Your Themabinti Services Hub verification code is: $otp. It expires in 5 minutes.";
        //     SMSGatewayService::send($user->phone_number, $message);
        //     return response()->json(['message' => 'Verification code sent to your phone number.'], 200);
        // } catch (\Exception $e) {
        //     \Log::error("SMS sending failed for user {$user->id}: " . $e->getMessage());
        //     return response()->json(['message' => 'Failed to send verification code. Please try again.'], 500);
        // }

        // For now, simulate success:
        \Log::info("Simulated OTP for user {$user->id} ({$user->phone_number}): $otp");
        return response()->json(['message' => 'Verification code sent to your phone number (simulated).'], 200);
    }

    public function checkPhoneVerification(PhoneVerificationCheckRequest $request) // Use Form Request
    {
        $user = $request->user();

        if ($user->phone_verified_at) {
            return response()->json(['message' => 'Phone number already verified.'], 200);
        }

        $otpKey = 'otp_' . $user->id . '_' . $user->phone_number;
        $storedOtp = Cache::get($otpKey);

        if (!$storedOtp || $request->otp != $storedOtp) {
            return response()->json(['message' => 'Invalid or expired verification code.'], 400);
        }

        $user->phone_verified_at = now();
        $user->save();
        Cache::forget($otpKey); // Remove OTP after successful verification

        return response()->json(['message' => 'Phone number verified successfully!'], 200);
    }
}