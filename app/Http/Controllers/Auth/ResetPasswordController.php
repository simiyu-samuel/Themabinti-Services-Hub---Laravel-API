<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // <-- Needed for hashing if you manually handle password update
use Illuminate\Support\Facades\Password; // <-- Import the Password facade
use Illuminate\Validation\ValidationException; // <-- Import ValidationException

class ResetPasswordController extends Controller
{
    /**
     * Reset the given user's password.
     * Overrides Laravel's default trait behavior for API JSON response.
     */
    public function reset(Request $request)
    {
        // Standard Laravel validation for password reset
        $request->validate([
            'token' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        // Use the Password facade to reset the password
        $response = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password); // Or use $user->forceFill(['password' => Hash::make($password)])->save();
                $user->save();
            }
        );

        // Handle success/failure based on the response status
        if ($response === Password::PASSWORD_RESET) {
            return response()->json([
                'message' => trans($response),
                'status' => $response,
            ], 200);
        }

        // If the response is a failure, throw a validation exception
        throw ValidationException::withMessages([
            'email' => [trans($response)], // Or 'token' depending on specific error
        ]);
    }
}