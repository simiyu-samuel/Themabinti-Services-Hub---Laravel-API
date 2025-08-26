<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Model Imports for Route Model Binding and Querying in Closures
use App\Models\Service;
use App\Models\Blog;
use App\Models\Appointment;
use App\Models\User; // For admin routes if passing user directly
use App\Http\Controllers\Auth\AuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
// In web.php or routes file
Route::get('/run-migrations', function () {
    try {
        Artisan::call('migrate');
        Artisan::call('db:seed');
        
        return response()->json([
            'status' => 'success',
            'migration_output' => Artisan::output()
        ]);
    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});