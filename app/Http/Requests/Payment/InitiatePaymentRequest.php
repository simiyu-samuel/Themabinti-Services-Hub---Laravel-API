<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // Only authenticated users can initiate payments
    }

    public function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:1|max:150000', // M-Pesa limits
            'phone_number' => 'required|string|regex:/^2547[0-9]{8}$/', // Kenyan Safaricom number
            'payment_type' => ['required', Rule::in(['seller_registration', 'package_upgrade', 'service_payment'])],
            'package_type' => ['nullable', 'required_if:payment_type,seller_registration,package_upgrade', Rule::in(['basic', 'standard', 'premium'])],
            'service_id' => 'nullable|required_if:payment_type,service_payment|exists:services,id',
            'appointment_id' => 'nullable|required_if:payment_type,service_payment|exists:appointments,id', // Link payment to appointment if applicable
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'The phone number must be a valid Kenyan Safaricom number starting with 2547.',
            'package_type.required_if' => 'Package type is required for seller registration or package upgrade.',
            'service_id.required_if' => 'Service ID is required for service payments.',
            'appointment_id.required_if' => 'Appointment ID is required for service payments.',
        ];
    }
}