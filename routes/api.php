<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController; // Will create this
use App\Http\Controllers\ServiceController; // Will create this
use App\Http\Controllers\AppointmentController; // Will create this
use App\Http\Controllers\GeneralBookingController;
use App\Http\Controllers\ContactController; // Will create this
use App\Http\Controllers\BlogController; // Will create this
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\Admin; // Namespace for admin controllers
use App\Http\Controllers\PublicSettingsController;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group which already includes Sanctum
| stateful and 'throttle:api'.
|
*/
// Route::get('/run-migrations', function () {
//     try {
//         Artisan::call('migrate');
//         // Artisan::call('db:seed');
     
//         return response()->json([
//             'status' => 'success',
//             'migration_output' => Artisan::output()
//         ]);
//     } catch (Exception $e) {
//         return response()->json([
//             'status' => 'error',
//             'message' => $e->getMessage()
//         ], 500);
//     }
// });

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    return response()->json(['message' => 'All Laravel caches cleared successfully. REMOVE THIS ROUTE AFTER USE!']);
});
    
// Public Authentication & Password Reset Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ResetPasswordController::class, 'reset']);

// Email Verification Routes (Publicly accessible, but verification link is signed)
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::post('/email/resend', [VerificationController::class, 'resend']); // Email for resend is in request body

// Public Service & Blog Listing Routes (details are also public)
Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{service}', [ServiceController::class, 'show']);
Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/{slug}', [BlogController::class, 'show']);

// Public Contact Form
Route::post('/contact', [ContactController::class, 'store']);

Route::post('/general-bookings', [GeneralBookingController::class, 'store']);

// Public Settings (e.g., dynamic package prices for registration page)
Route::get('/public/settings/package-prices', [PublicSettingsController::class, 'getPackagePrices']);

// M-Pesa Callback URL (MUST be public for Safaricom to hit it)
Route::post('/mpesa/callback', [PaymentController::class, 'mpesaCallback']);

// Authenticated User Routes (Require Sanctum token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    
     // Password Change & Account Deletion h
    Route::post('/user/password', [UserController::class, 'changePassword']); // NEW
    Route::delete('/user/account', [UserController::class, 'deleteAccount']); // NEW

    // User Profile
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::post('/user/profile-image', [UserController::class, 'uploadProfileImage']);

    // // Phone Verification (User must be authenticated)
    // Route::post('/phone/verify/request', [VerificationController::class, 'requestPhoneVerification']);
    // Route::post('/phone/verify/check', [VerificationController::class, 'checkPhoneVerification']);

    // Payments & Seller Packages
    Route::post('/payments/initiate', [PaymentController::class, 'initiateStkPush']);
    Route::get('/payments/status/{checkoutRequestId}', [PaymentController::class, 'getPaymentStatus']);
    Route::get('/payments/history', [PaymentController::class, 'userPaymentHistory']); // User-specific history

    // Customer Dashboard Insights (NEW)
    Route::get('/customer/dashboard/insights', [CustomerDashboardController::class, 'insights']);
    
    // Seller-specific Routes (Middleware: ensure user is a seller and active)
    Route::middleware(['is_seller', 'email_verified'])->group(function () {
        Route::get('/seller/dashboard/insights', [SellerDashboardController::class, 'insights']);
        // Service Management
        Route::apiResource('services', ServiceController::class)->except(['index', 'show']); // Handled by policy below
        Route::post('/services/{service}/media', [ServiceController::class, 'uploadMedia']); // Separate media upload endpoint
    });

    // Customer/Seller Appointment Routes (Handled by policies)
    Route::apiResource('appointments', AppointmentController::class); // Policies will manage access based on role
    // User (Customer/Seller) Personal Settings (NEWLY ADDED ROUTES)
    Route::get('/settings', [UserSettingsController::class, 'show']);   // Get authenticated user's settings
    Route::put('/settings', [UserSettingsController::class, 'update']); // Update authenticated user's settings
});

// Admin Routes (Middleware: ensure user is an admin)
Route::prefix('admin')->middleware(['auth:sanctum', 'is_admin'])->group(function () {
    // User Management (Admins can manage all users)
    // Update the destroy route to use the new request
    Route::delete('/users/{user}', [Admin\AdminUserController::class, 'destroy']); // Uses AdminDeleteUserRequest
    Route::apiResource('users', Admin\AdminUserController::class)->except(['destroy']); // Exclude default destroy to use custom one
    
    // Admin General Booking Management (NEW)
    Route::apiResource('general-bookings', Admin\AdminGeneralBookingController::class);

    // NEW: Restore Soft-Deleted User
    Route::post('/users/{id}/restore', [Admin\AdminUserController::class, 'restore']); // Use user ID from URL
    // NEW: Force Delete User (Permanent)
    Route::delete('/users/{id}/force-delete', [Admin\AdminUserController::class, 'forceDelete']); // Use user ID from URL and AdminDeleteUserRequest
    Route::get('/dashboard', [Admin\AdminDashboardController::class, 'index']);
    // Route::apiResource('users', Admin\AdminUserController::class);
    Route::apiResource('services', Admin\AdminServiceController::class)->except(['store', 'destroy']); // Admin manages activity, not creation/deletion
    Route::apiResource('payments', Admin\AdminPaymentController::class)->only(['index', 'show']);
    Route::apiResource('appointments', Admin\AdminAppointmentController::class)->except(['store', 'destroy']);
    Route::apiResource('contacts', Admin\AdminContactController::class)->except(['store', 'create', 'edit']);
    Route::post('/contacts/{contact}/respond', [Admin\AdminContactController::class, 'respond']);
    Route::apiResource('blogs', Admin\AdminBlogController::class);
     // Admin Global Settings
    Route::get('/settings', [Admin\AdminSettingsController::class, 'index']); // Get all global settings
    Route::put('/settings', [Admin\AdminSettingsController::class, 'update']); // Update global settings
    
    // Admin route to create storage link
    Route::post('/create-storage-link', [Admin\AdminSettingsController::class, 'createStorageLink']);

});