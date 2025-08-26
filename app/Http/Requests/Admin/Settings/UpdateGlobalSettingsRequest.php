<?php

namespace App\Http\Requests\Admin\Settings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGlobalSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'site_name' => ['nullable', 'string', 'max:255'],
            'support_email' => ['nullable', 'email', 'max:255'],
            'seller_package_price_basic' => ['nullable', 'numeric', 'min:0'],
            'seller_package_price_standard' => ['nullable', 'numeric', 'min:0'],
            'seller_package_price_premium' => ['nullable', 'numeric', 'min:0'],
            'mpesa_consumer_key' => ['nullable', 'string', 'max:255'],
            'mpesa_consumer_secret' => ['nullable', 'string', 'max:255'],
            'mpesa_passkey' => ['nullable', 'string', 'max:255'],
            'mpesa_shortcode' => ['nullable', 'string', 'max:255'],
        ];
    }
}