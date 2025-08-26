<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // If the user is a seller, they must have a verified phone number.
        if ($request->user() && $request->user()->isSeller() && ! $request->user()->phone_verified_at) {
            return response()->json([
                'message' => 'Your phone number is not verified. Please verify your phone number to continue.',
                'phone_verified' => false,
            ], 403);
        }

        return $next($request);
    }
}