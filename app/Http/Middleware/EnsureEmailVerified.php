<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // If the user is a seller, they must have a verified email.
        if ($request->user() && $request->user()->isSeller() && ! $request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Your email address is not verified. Please verify your email to continue.',
                'email_verified' => false,
            ], 403);
        }

        return $next($request);
    }
}