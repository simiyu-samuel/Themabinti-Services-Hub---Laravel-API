<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password; // <-- Import the Password facade
use Illuminate\Validation\ValidationException; // <-- Import ValidationException

class ForgotPasswordController extends Controller
{
    /**
     * Send a reset link to the given user.
     * Overrides Laravel's default trait behavior for API JSON response.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Standard Laravel email validation for password reset
        $request->validate(['email' => ['required', 'email']]);

        // Use the Password facade to send the reset link
        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );

        // Handle success/failure based on the response status
        if ($response === Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => trans($response),
                'status' => $response,
            ], 200);
        }

        // If the response is a failure (e.g., user not found), throw a validation exception
        throw ValidationException::withMessages([
            'email' => [trans($response)],
        ]);
    }
}