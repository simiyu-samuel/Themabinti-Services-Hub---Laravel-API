<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSeller
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->isSeller()  || $request->user() && $request->user()->isAdmin()) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized. Only sellers/admins can perform this action.'], 403);
    }
}