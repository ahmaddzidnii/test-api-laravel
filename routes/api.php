<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
    'controller' => AuthController::class,
], function () {
    // Public routes
    Route::post('/signin', 'login');
    Route::post('/refresh', 'refreshToken');

    // Protected routes
    Route::middleware(['jwt.extract', 'jwt.required'])->group(function () {
        Route::get('/session', 'getUserInfo');
        Route::post('/signout', 'logout');
    });
});
