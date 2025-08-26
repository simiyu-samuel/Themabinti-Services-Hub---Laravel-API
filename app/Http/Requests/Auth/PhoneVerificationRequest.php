<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PhoneVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // User must be authenticated and phone not yet verified
        return auth()->check() && !auth()->user()->phone_verified_at;
    }

    public function rules(): array
    {
        return [
            // No specific input for this, it just triggers the OTP send
        ];
    }
}