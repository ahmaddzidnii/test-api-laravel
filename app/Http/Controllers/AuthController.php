<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\LoginRequest;
use App\HttpResponses;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

        $refreshToken = Str::random(128);

        // Store session in database
        Session::create([
            'user_id' => $user->id,
            'refresh_token' => hash('sha256', $refreshToken),
            'device_info' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'expires_at' => now()->addDays(30),
        ]);

        return ResponseHelper::withAuthCookies(
            $this->success([
                'accessToken' => $accessToken,
                'refreshToken' => $refreshToken,
            ], 'User loggedin successfully', 201),
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
        $user = $request->user();
        $refreshToken = $this->getRefreshTokenFromRequest($request);

        if (!$refreshToken) {
            return $this->error('Refresh token not provided', 400);
        }

        // Delete session from database
        if ($refreshToken) {
            Session::where('user_id', $user->id)
                ->where('refresh_token', hash('sha256', $refreshToken))
                ->delete();
        }

        // Clear cookies and return response
        return ResponseHelper::clearAuthCookies(
            $this->success(null, 'Logged out successfully')
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
        $refreshToken = $this->getRefreshTokenFromRequest($request);

        if (!$refreshToken) {
            return $this->error('Refresh token not provided', 400);
        }

        $hashedToken = hash('sha256', $refreshToken);

        // Find valid session
        $session = Session::where('refresh_token', $hashedToken)
            ->where('expires_at', '>', now())
            ->first();

        if (!$session) {
            return $this->error('Invalid or expired refresh token', 401);
        }

        $user = $session->user;
        $newAccessToken = Auth::guard('api')->login($user);

        $newRefreshToken = Str::random(128);

        // Update session
        $session->update([
            'refresh_token' => hash('sha256', $newRefreshToken),
            'device_info' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'expires_at' => now()->addDays(30),
        ]);

        return ResponseHelper::withAuthCookies(
            $this->success([
                'accessToken' => $newAccessToken,
                'refreshToken' => $newRefreshToken,
            ], 'Tokens refreshed successfully'),
            (string) $newAccessToken,
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
        $user = $request->user();

        return $this->success([
            'userId' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'avatar' => $user->avatar,
            'role' => $user->role,
        ], "Success");
    }

    private function getRefreshTokenFromRequest(Request $request): ?string
    {
        // Check Authorization header first (Bearer token)
        $bearerToken = $request->bearerToken();
        if ($bearerToken) {
            return $bearerToken;
        }

        // Fallback to cookie
        return $request->cookie('refreshToken');
    }
}
