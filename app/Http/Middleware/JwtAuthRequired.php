<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthRequired
{
    /**
     * Handle an incoming request.
     * This middleware ensures that the request has an authenticated user.
     * Use this AFTER JwtFromCookieOrHeader middleware.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return response()->json([
                'statusCode' => 401,
                'message' => 'Authentication required please provide a valid JWT token.',
                'data' => null,
            ], 401);
        }

        return $next($request);
    }
}
