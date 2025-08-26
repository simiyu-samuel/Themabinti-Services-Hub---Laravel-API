<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * No default casts needed here. Values will be handled by the service.
     * This ensures the 'value' column is treated as a string by default,
     * allowing us to store numbers, booleans, or JSON strings.
     */
    // protected $casts = [
    //     // 'value' => 'array', // DO NOT use if value can be scalar (string, int, float)
    // ];

    /**
     * Get a setting by key, applying appropriate type casting.
     */
    public static function getTypedValue(string $key)
    {
        $setting = static::where('key', $key)->first();
        if (!$setting) {
            return null; // Or retrieve from config('themabinti.admin_defaults.'.$key) as fallback
        }

        // Apply type casting based on common expected types
        if (is_numeric($setting->value)) {
            return (float)$setting->value; // Cast to float for prices
        }
        if (in_array($setting->value, ['true', 'false'])) {
            return (bool)$setting->value; // Cast boolean strings
        }
        // If it's stored as a JSON string and expected as array/object
        // $decoded = json_decode($setting->value, true);
        // if (json_last_error() === JSON_ERROR_NONE) {
        //     return $decoded;
        // }

        return $setting->value; // Default to string
    }
}