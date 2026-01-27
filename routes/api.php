<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\JwtFromCookieOrHeader;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
    'controller' => AuthController::class,
], function () {
    // Public routes
    Route::post('/signin', 'login');
    Route::post('/refresh', 'refreshToken');

    // Protected routes - using custom middleware directly
    Route::middleware(JwtFromCookieOrHeader::class)->group(function () {
        Route::get('/session', 'getUserInfo');
        Route::post('/signout', 'logout');
    });
});
