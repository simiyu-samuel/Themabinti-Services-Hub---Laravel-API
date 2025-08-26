<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PhoneVerificationCheckRequest extends FormRequest
{
    public function authorize(): bool
    {
        // User must be authenticated and phone not yet verified
        return auth()->check() && !auth()->user()->phone_verified_at;
    }

    public function rules(): array
    {
        return [
            'otp' => ['required', 'string', 'min:6', 'max:6'], // Assuming 6-digit OTP
        ];
    }
}