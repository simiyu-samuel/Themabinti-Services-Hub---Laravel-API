<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Exclude all API routes from CSRF verification
        'api/*',
        
        // Specific auth endpoints (if needed)
        '/login',
        '/register',
        '/logout',
        
        // Webhooks or third-party endpoints
        // 'webhook/*',
        // 'payment/callback',
    ];
}