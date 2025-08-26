<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class AdminSettingsService
{
    const CACHE_KEY = 'admin_settings';
    const CACHE_TTL = 60 * 24; // Cache for 24 hours (or longer for static settings)

    /**
     * Get a specific global setting.
     *
     * @param string $key The setting key (e.g., 'site_name', 'seller_package_price_basic')
     * @return mixed The setting value, or default from config if not found in DB
     */
    public function get(string $key): mixed
    {
        // Try to get from cache first
        $allSettings = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()->keyBy('key');
        });

        // Get value from DB, then cast, otherwise fallback to config default
        $setting = $allSettings->get($key);
        if ($setting) {
            return $setting->getTypedValue($key) ?? Config::get('themabinti.admin_defaults.' . $key);
        }

        // If not in DB, return from config defaults
        return Config::get('themabinti.admin_defaults.' . $key);
    }

    /**
     * Get all global settings.
     *
     * @return array An associative array of all settings
     */
    public function getAll(): array
    {
        $settings = [];
        foreach (Config::get('themabinti.admin_defaults') as $key => $defaultValue) {
            // Fetch each setting through the 'get' method to ensure caching and type casting
            $settings[$key] = $this->get($key);
        }
        return $settings;
    }

    /**
     * Update a specific global setting.
     *
     * @param string $key The setting key
     * @param mixed $value The new value for the setting
     * @return Setting The updated/created setting model
     */
    public function set(string $key, mixed $value): Setting
    {
        // Ensure boolean values are stored as 0 or 1 for consistent retrieval
        $storedValue = is_bool($value) ? (int)$value : $value;

        $setting = Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $storedValue]
        );

        // Clear cache after update
        Cache::forget(self::CACHE_KEY);

        return $setting;
    }

    /**
     * Update multiple global settings.
     *
     * @param array $data Associative array of settings to update (key => value)
     * @return array Updated settings
     */
    public function setMany(array $data): array
    {
        $updatedSettings = [];
        foreach ($data as $key => $value) {
            // Only allow setting keys that are defined in admin_defaults to prevent arbitrary keys
            if (Config::has('themabinti.admin_defaults.' . $key)) {
                $updatedSettings[$key] = $this->set($key, $value)->value;
            }
        }
        return $updatedSettings;
    }
}