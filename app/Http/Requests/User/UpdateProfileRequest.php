<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // Only authenticated users can update their profile
    }

    public function rules(): array
    {
        $userId = $this->user()->id;
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'phone_number' => ['sometimes', 'string', 'regex:/^2547[0-9]{8}$/', Rule::unique('users')->ignore($userId)],
            'location' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'The phone number must be a valid Kenyan Safaricom number starting with 2547.',
        ];
    }
}