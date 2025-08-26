<?php

namespace App\Http\Requests\Admin\GeneralBooking;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGeneralBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'required', Rule::in(['pending', 'assigned', 'rejected', 'completed'])],
            'admin_notes' => ['nullable', 'string', 'max:1000'],
            'assigned_seller_id' => ['nullable', 'exists:users,id',
                Rule::requiredIf(fn () => $this->input('status') === 'assigned'),
                function ($attribute, $value, $fail) {
                    if ($value && ($seller = \App\Models\User::find($value)) && !$seller->isSeller()) {
                        $fail('The assigned user must be a seller.');
                    }
                }
            ],
        ];
    }
}