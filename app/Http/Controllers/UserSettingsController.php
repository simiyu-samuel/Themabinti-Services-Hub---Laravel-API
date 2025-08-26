<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserSettings\UpdateSettingsRequest; // Import the Form Request

class UserSettingsController extends Controller
{
    /**
     * Get the authenticated user's current settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $user = $request->user();

        // Return existing settings from the 'settings' JSON column,
        // or a default empty array if the column is currently null.
        return response()->json(['settings' => $user->settings ?? []]);
    }

    /**
     * Update the authenticated user's settings.
     *
     * @param  \App\Http\Requests\UserSettings\UpdateSettingsRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSettingsRequest $request)
    {
        $user = $request->user();

        // Retrieve the current settings from the user's 'settings' JSON column.
        // Default to an empty array if the column is null to ensure array_merge works correctly.
        $currentSettings = $user->settings ?? [];

        // Get only the validated data that was sent in the request.
        // $request->validated() will contain only the fields that were present in the request
        // payload AND passed their validation rules.
        $incomingSettings = $request->validated();

        // Merge the current settings with the incoming settings.
        // array_merge will:
        // - Overwrite existing keys with new values from $incomingSettings.
        // - Add new keys from $incomingSettings that were not in $currentSettings.
        // - Retain keys from $currentSettings that were not present in $incomingSettings.
        // This effectively performs a partial update.
        $newSettings = array_merge($currentSettings, $incomingSettings);

        // Assign the merged settings back to the user's settings column.
        $user->settings = $newSettings;
        $user->save(); // Persist the changes to the database.

        return response()->json([
            'message' => 'Settings updated successfully.',
            'settings' => $user->settings, // Return the full, updated settings object
        ]);
    }
}