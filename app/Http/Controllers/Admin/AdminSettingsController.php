<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting; // Still needed for type-hint if used in helper methods
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Settings\UpdateGlobalSettingsRequest;
use App\Services\AdminSettingsService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class AdminSettingsController extends Controller
{
    protected AdminSettingsService $adminSettingsService;

    public function __construct(AdminSettingsService $adminSettingsService)
    {
        $this->adminSettingsService = $adminSettingsService;
    }

    /**
     * Get all global settings.
     */
    public function index()
    {
        // Use the service to get all settings
        $settings = $this->adminSettingsService->getAll();
        return response()->json(['settings' => $settings]);
    }
    
        /**
     * Create the storage symbolic link.
     * This is an admin-only action, typically run once after deployment.
     * Requires the web server user to have appropriate permissions to create symlinks.
     */
    public function createStorageLink(Request $request)
    {
        // Add a security check: For highly sensitive actions like this,
        // you might want to require the admin's password in the request body.
        // For now, it's just middleware protected.
        
        try {
            // Attempt to delete any existing public/storage folder/file first
            // to prevent 'File exists' errors on symlink creation.
            if (file_exists(public_path('storage'))) {
                // If it's a directory, try to remove it
                if (is_dir(public_path('storage'))) {
                    // Laravel's Filesystem facade can remove directories
                    \Illuminate\Support\Facades\File::deleteDirectory(public_path('storage'));
                    Log::info('Existing public/storage directory removed before relinking.');
                } elseif (is_file(public_path('storage'))) {
                    // If it's a file, remove it
                    \Illuminate\Support\Facades\File::delete(public_path('storage'));
                    Log::info('Existing public/storage file removed before relinking.');
                }
            }

            Artisan::call('storage:link');
            Log::info('Storage link created successfully via Admin API.');
            return response()->json(['message' => 'Storage link created successfully. Images should now be visible.']);
        } catch (\Exception $e) {
            Log::error('Error creating storage link via Admin API: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Failed to create storage link. Error: ' . $e->getMessage() . '. Ensure web server user has permissions to create symlinks.'], 500);
        }
    }

    /**
     * Update global settings.
     * Expects specific keys for update.
     */
    public function update(UpdateGlobalSettingsRequest $request)
    {
        $validatedData = $request->validated();
        
        // Use the service to update multiple settings
        $updatedSettings = $this->adminSettingsService->setMany($validatedData);

        return response()->json([
            'message' => 'Global settings updated successfully.',
            'settings' => $updatedSettings,
        ]);
    }
}