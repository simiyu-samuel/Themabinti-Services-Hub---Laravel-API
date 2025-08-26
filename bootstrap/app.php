<?php

// bootstrap/app.php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserIsAdmin;     
use App\Http\Middleware\EnsureUserIsSeller;   
use App\Http\Middleware\EnsureEmailVerified;  
use App\Http\Middleware\EnsurePhoneVerified;  
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',     // Enable web routes
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // Web Middleware Group (with CSRF protection)
        $middleware->web([
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // API Middleware Group 
        $middleware->api(prepend: [
            HandleCors::class,
            // EnsureFrontendRequestsAreStateful::class, // Enables stateful auth for SPA
            'throttle:api',
        ]);

        // Register custom route middleware aliases
        $middleware->alias([
            'is_admin' => EnsureUserIsAdmin::class,
            'is_seller' => EnsureUserIsSeller::class,
            'email_verified' => EnsureEmailVerified::class,
            // 'phone_verified' => EnsurePhoneVerified::class,
        ]);

        // Exclude specific API endpoints from CSRF (for third-party integrations)
        $middleware->validateCsrfTokens(except: [
            'api/webhook/*',
            'api/external/*',
            // Don't exclude all api/* if you want stateful SPA auth
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();