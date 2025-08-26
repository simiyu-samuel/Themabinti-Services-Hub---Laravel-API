<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Anyone can register
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'unique:users', 'regex:/^2547[0-9]{8}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', Rule::in(['customer', 'seller'])],
            'seller_package' => ['nullable', 'required_if:user_type,seller', Rule::in(['basic', 'standard', 'premium'])],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_number.regex' => 'The phone number must be a valid Kenyan Safaricom number starting with 2547.',
            'seller_package.required_if' => 'A seller package is required for sellers.',
        ];
    }
}