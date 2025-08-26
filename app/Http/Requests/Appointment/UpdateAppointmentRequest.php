<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled by AppointmentPolicy 'update' method
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'status' => ['sometimes', 'required', Rule::in(['pending', 'confirmed', 'completed', 'cancelled'])],
            'appointment_date' => ['sometimes', 'required', 'date_format:Y-m-d H:i:s', 'after_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        $appointment = $this->route('appointment'); // Get the current appointment instance
        if ($appointment) {
            // If customer is updating, only 'cancelled' status is allowed
            if ($this->user()->id === $appointment->customer_id && $this->input('status') && $this->input('status') !== 'cancelled') {
                $rules['status'][] = Rule::in(['cancelled']); // Restrict customer to only cancelling
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'appointment_date.after_or_equal' => 'Appointment date cannot be in the past.',
            'status.in' => 'Invalid status provided.',
        ];
    }
}