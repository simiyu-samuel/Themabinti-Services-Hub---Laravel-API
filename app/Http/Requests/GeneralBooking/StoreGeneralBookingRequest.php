<?php

namespace App\Http\Requests\GeneralBooking;

use Illuminate\Foundation\Http\FormRequest;

class StoreGeneralBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public endpoint, anyone can submit
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'regex:/^2547[0-9]{8}$/'], // Kenyan format
            'customer_email' => ['required', 'string', 'email', 'max:255'],
            'preferred_date_time' => ['required', 'date_format:Y-m-d H:i:s', 'after_or_equal:now'], // Must be in future
            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_phone.regex' => 'The phone number must be a valid Kenyan Safaricom number starting with 2547.',
            'preferred_date_time.after_or_equal' => 'The preferred date and time cannot be in the past.',
        ];
    }
}