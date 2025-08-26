<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AdminSettingsService; // Import the AdminSettingsService

class PublicSettingsController extends Controller
{
    /**
     * The AdminSettingsService instance.
     *
     * @var \App\Services\AdminSettingsService
     */
    protected AdminSettingsService $adminSettingsService;

    /**
     * Create a new PublicSettingsController instance.
     *
     * @param \App\Services\AdminSettingsService $adminSettingsService The AdminSettingsService instance via dependency injection.
     * @return void
     */
    public function __construct(AdminSettingsService $adminSettingsService)
    {
        $this->adminSettingsService = $adminSettingsService;
    }

    /**
     * Get public dynamic settings, specifically package prices, for unauthenticated users.
     *
     * This endpoint is designed to provide key public configuration values
     * that might be managed by the admin, without requiring user authentication.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPackagePrices(Request $request)
    {
        // Retrieve package prices using the AdminSettingsService.
        // The service handles fetching from the database (if set by admin)
        // or falling back to defaults from config/themabinti.php.
        $prices = [
            'basic' => $this->adminSettingsService->get('seller_package_price_basic'),
            'standard' => $this->adminSettingsService->get('seller_package_price_standard'),
            'premium' => $this->adminSettingsService->get('seller_package_price_premium'),
        ];

        // Return the prices as a JSON response.
        return response()->json(['package_prices' => $prices]);
    }

    /**
     * You could add more public settings methods here if needed,
     * e.g., getContactInfo(), getSiteStatus(), etc.
     */
    // public function getContactInfo(Request $request)
    // {
    //     $contactInfo = [
    //         'support_email' => $this->adminSettingsService->get('support_email'),
    //         'phone_number' => $this->adminSettingsService->get('public_contact_phone'),
    //     ];
    //     return response()->json(['contact_info' => $contactInfo]);
    // }
}