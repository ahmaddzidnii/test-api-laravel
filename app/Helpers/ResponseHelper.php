<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    /**
     * Add cookies to a JSON response
     *
     * @param JsonResponse $response
     * @param array $cookies Array of cookies with format: ['name' => ['value' => '', 'minutes' => 60, ...]]
     * @return JsonResponse
     */
    public static function withCookies(JsonResponse $response, array $cookies): JsonResponse
    {
        foreach ($cookies as $name => $config) {
            $response->cookie(
                $name,
                $config['value'] ?? '',
                $config['minutes'] ?? 60,
                $config['path'] ?? '/',
                $config['domain'] ?? null,
                $config['secure'] ?? false,
                $config['httpOnly'] ?? true,
                $config['raw'] ?? false,
                $config['sameSite'] ?? 'lax'
            );
        }

        return $response;
    }

    /**
     * Add JWT authentication cookies
     *
     * @param JsonResponse $response
     * @param string $accessToken
     * @param string $refreshToken
     * @param int $accessTokenMinutes
     * @return JsonResponse
     */
    public static function withAuthCookies(
        JsonResponse $response,
        string $accessToken,
        string $refreshToken,
        int $accessTokenMinutes = 60
    ): JsonResponse {
        return $response
            ->cookie('accessToken', $accessToken, $accessTokenMinutes, '/', null, false, true, false, 'lax')
            ->cookie('refreshToken', $refreshToken, 60 * 24 * 30, '/', null, false, true, false, 'lax');
    }

    /**
     * Clear authentication cookies
     *
     * @param JsonResponse $response
     * @return JsonResponse
     */
    public static function clearAuthCookies(JsonResponse $response): JsonResponse
    {
        return $response
            ->cookie('accessToken', '', -1, '/', null, false, true, false, 'lax')
            ->cookie('refreshToken', '', -1, '/', null, false, true, false, 'lax');
    }
}
