<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtFromCookieOrHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log untuk debug
        Log::info('JwtFromCookieOrHeader middleware executed');

        $token = null;

        // Try to get token from Authorization header first
        $authHeader = $request->header('Authorization', '');
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            $token = $matches[1];
            Log::info('Token found in header');
        }

        // If no token in header, try to get from cookie
        if (!$token) {
            // Try different ways to get cookie
            $token = $request->cookie('access_token')
                ?? $request->cookies->get('access_token')
                ?? $_COOKIE['access_token'] ?? null;

            if ($token) {
                Log::info('Token found in cookie');
            }
        }

        // If still no token, return unauthorized
        if (!$token) {
            Log::info('No token found');
            return response()->json([
                'message' => 'Token not provided',
            ], 401);
        }

        try {
            // Set the token manually
            JWTAuth::setToken($token);

            // Try to authenticate user with the token
            $user = JWTAuth::authenticate();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            // Set authenticated user in request
            $request->setUserResolver(function () use ($user) {
                return $user;
            });
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['message' => 'Token expired'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['message' => 'Token invalid'], 401);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['message' => 'Token error: ' . $e->getMessage()], 401);
        }

        return $next($request);
    }
}
