<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Service; // To check if service exists and is active
use Illuminate\Support\Carbon;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled by AppointmentPolicy 'create' method (customer role check)
        return true;
    }

    public function rules(): array
    {
        return [
            'service_id' => [
                'required',
                'exists:services,id',
                function ($attribute, $value, $fail) {
                    $service = Service::find($value);
                    if (!$service || !$service->is_active) {
                        $fail("The selected service is not active or does not exist.");
                    }
                    if ($service && $service->user_id === $this->user()->id) {
                        $fail("You cannot book your own service.");
                    }
                },
            ],
            'appointment_date' => [
                'required',
                'date_format:Y-m-d H:i:s',
                'after_or_equal:now', // Cannot book in the past
                function ($attribute, $value, $fail) {
                    $appointmentTime = Carbon::parse($value);
                    // Optional: Add more complex availability checks here
                    // e.g., check seller's working hours, check for existing overlapping appointments
                    // This would likely involve fetching seller's schedule or existing appointments
                    // and is a significant feature on its own. For now, a simple future date check.
                }
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
            // total_amount is calculated on backend from service price
        ];
    }

    public function messages(): array
    {
        return [
            'appointment_date.after_or_equal' => 'Appointment date cannot be in the past.',
        ];
    }
}