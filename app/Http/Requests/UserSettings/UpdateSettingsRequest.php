<?php

namespace App\Http\Requests\UserSettings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // Authenticated user
    }

    public function rules(): array
    {
        return [
            // General Notification Settings (for all users: customer/seller/admin)
            'notification_email_enabled' => ['nullable', 'boolean'],
            'notification_sms_enabled' => ['nullable', 'boolean'], // Only if SMS integration exists
            'notification_promo_email' => ['nullable', 'boolean'], // New: Receive promotional emails

            // Privacy Settings (for all users)
            'privacy_show_phone' => ['nullable', 'boolean'], // Whether phone number is publicly visible on profile/listings

            // Seller-Specific Business Settings (apply only if user_type is 'seller')
            'seller_booking_auto_confirm' => ['nullable', 'boolean'], // New: Auto-confirm new appointments
            'seller_booking_lead_time_hours' => ['nullable', 'integer', 'min:0', 'max:168'], // New: Minimum notice for bookings (up to 1 week)
            'seller_cancellation_policy' => ['nullable', 'string', 'max:1000'], // New: Custom cancellation policy text for their services
            'seller_profile_visibility' => ['nullable', 'string', 'in:public,private'], // New: Make entire seller profile/listings public/private
            'seller_default_service_duration' => ['nullable', 'integer', 'min:15', 'max:240'], // New: Default duration for new services
        ];
    }
}