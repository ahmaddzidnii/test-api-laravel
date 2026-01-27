<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\LoginRequest;
use App\HttpResponses;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use HttpResponses;
    /**
     * Handle a login request to the application.
     *
     * @param  \App\Http\Requests\LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $accessToken = $request->authenticate();
        $user = Auth::guard('api')->user();

        // Generate refresh token
        $refreshToken = Str::random(64);

        // Store session in database
        Session::create([
            'user_id' => $user->id,
            'refresh_token' => hash('sha256', $refreshToken),
            'device_info' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'expires_at' => now()->addDays(30),
        ]);

        $response = [
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
                'avatar_image_id' => $user->avatar_image_id,
            ],
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ];

        // Use helper to add cookies
        return ResponseHelper::withAuthCookies(
            response()->json($response),
            $accessToken,
            $refreshToken,
            Auth::guard('api')->factory()->getTTL()
        );
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Get user from request
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Get refresh token from cookie or request body
        $refreshToken = $request->cookie('refresh_token') ?? $request->input('refresh_token');

        // Delete session from database
        if ($refreshToken) {
            Session::where('user_id', $user->id)
                ->where('refresh_token', hash('sha256', $refreshToken))
                ->delete();
        }

        // Clear cookies and return response
        return ResponseHelper::clearAuthCookies(
            response()->json(['message' => 'Successfully logged out'])
        );
    }

    /**
     * Refresh a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refreshToken(Request $request)
    {
        // Get refresh token from cookie or request body
        $refreshToken = $request->cookie('refresh_token') ?? $request->input('refresh_token');

        if (!$refreshToken) {
            return response()->json([
                'message' => 'Refresh token not provided',
            ], 400);
        }

        $hashedToken = hash('sha256', $refreshToken);

        // Find valid session
        $session = Session::where('refresh_token', $hashedToken)
            ->where('expires_at', '>', now())
            ->first();

        if (!$session) {
            return response()->json([
                'message' => 'Invalid or expired refresh token',
            ], 401);
        }

        // Generate new access token
        $user = $session->user;
        $newAccessToken = Auth::guard('api')->login($user);

        // Generate new refresh token
        $newRefreshToken = Str::random(64);

        // Update session
        $session->update([
            'refresh_token' => hash('sha256', $newRefreshToken),
            'device_info' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'expires_at' => now()->addDays(30),
        ]);

        $response = [
            'message' => 'Token refreshed successfully',
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ];

        // Use helper to add cookies
        return ResponseHelper::withAuthCookies(
            response()->json($response),
            (string)  $newAccessToken,
            $newRefreshToken,
            Auth::guard('api')->factory()->getTTL()
        );
    }

    /**
     * Get the authenticated User.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo(Request $request)
    {
        // Get user from request (set by middleware)
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Success ret'], 401);
        }

        return $this->success([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
            'avatar_image_id' => $user->avatar_image_id,
        ], "Successfully retrieved session data");
    }
}
