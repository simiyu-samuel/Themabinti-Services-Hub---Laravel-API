<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Added
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail // Implemented
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number', // Added
        'password',
        'user_type', // Added
        'seller_package', // Added
        'package_expiry_date', // Added
        'location', // Added
        'bio', // Added
        'profile_image', // Added
        'is_active', // Added
        'email_verified_at', // Added (for MustVerifyEmail)
        // 'phone_verified_at', // Added
        'pending_seller_package',
        'settings',
        
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 'phone_verified_at' => 'datetime', // Added
        'package_expiry_date' => 'datetime', // Added
        'is_active' => 'boolean', // Added
        'password' => 'hashed',
        'settings' => 'array',
    ];

    /**
     * Get the services provided by the user (if they are a seller).
     */
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    /**
     * Get the appointments booked by the user (if they are a customer).
     */
    public function bookedAppointments()
    {
        return $this->hasMany(Appointment::class, 'customer_id');
    }

    /**
     * Get the appointments provided by the user (if they are a seller).
     */
    public function providedAppointments()
    {
        return $this->hasMany(Appointment::class, 'seller_id');
    }

    /**
     * Get the payments made by the user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the blog posts created by the user (if they are an admin).
     */
    public function blogPosts()
    {
        return $this->hasMany(Blog::class, 'admin_id');
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * Check if the user is a seller.
     */
    public function isSeller(): bool
    {
        return $this->user_type === 'seller';
    }

    /**
     * Check if the user is a customer.
     */
    public function isCustomer(): bool
    {
        return $this->user_type === 'customer';
    }
    public function getSettingsAttribute($value): array
    {
        // Decode the JSON string to an array. If it's null or an invalid JSON string, default to empty array.
        $decoded = json_decode($value ?? '[]', true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Set the user's settings. Ensures it's stored as JSON string.
     */
    public function setSettingsAttribute(array $value): void
    {
        $this->attributes['settings'] = json_encode($value);
    }
}