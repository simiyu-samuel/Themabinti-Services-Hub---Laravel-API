<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home'; // Or your desired dashboard URL

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // This is where you would traditionally define explicit rate limiters
        // for route groups or individual routes. In Laravel 11, most of the
        // group definition happens in bootstrap/app.php.
        // The configureRateLimiting() method is still relevant here.
        $this->configureRateLimiting();

        // In Laravel 11, route loading is configured in bootstrap/app.php.
        // The methods like $this->routes(function () { ... }); are generally
        // no longer used here if your routes are defined in routes/*.php files.
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting(): void
    {
        // Default API rate limiter, applied to all routes in routes/api.php via bootstrap/app.php
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Specific rate limiter for login attempts (by email)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email)->response(function (Request $request, array $headers) {
                return response()->json([
                    'message' => 'Too many login attempts. Please try again later.',
                    'retry_after' => $headers['Retry-After'] ?? null,
                ], 429, $headers);
            });
        });

        // Specific rate limiter for verification actions (by user ID or IP)
        RateLimiter::for('verification', function (Request $request) {
            return Limit::perMinute(3)->by($request->user()?->id ?: $request->ip())->response(function (Request $request, array $headers) {
                return response()->json([
                    'message' => 'Too many verification attempts. Please wait before retrying.',
                    'retry_after' => $headers['Retry-After'] ?? null,
                ], 429, $headers);
            });
        });
    }
}