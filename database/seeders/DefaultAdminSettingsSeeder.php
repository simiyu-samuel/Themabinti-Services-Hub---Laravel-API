<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;

class DefaultAdminSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Iterate through the admin_defaults in your config and create settings
        foreach (Config::get('themabinti.admin_defaults') as $key => $defaultValue) {
            // Ensure boolean values are stored as 0 or 1
            $storedValue = is_bool($defaultValue) ? (int)$defaultValue : $defaultValue;

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $storedValue]
            );
        }
    }
}