<?php

namespace App\Http\Requests\Admin\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminDeleteUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Must be an admin
        if (!$this->user() || !$this->user()->isAdmin()) {
            return false;
        }
        // Admin cannot delete their own account
        if ($this->route('user') && $this->route('user')->id === $this->user()->id) {
            throw ValidationException::withMessages([
                'user' => ['You cannot delete your own admin account.']
            ]);
        }
        return true;
    }

    public function rules(): array
    {
        return [
            // Admin must provide their own password to confirm destructive action
            'admin_password' => [
                'required', 'string',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, $this->user()->password)) {
                        $fail('Your provided password does not match your admin account password.');
                    }
                },
            ],
        ];
    }
}