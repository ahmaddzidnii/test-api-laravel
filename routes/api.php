<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
], function () {
    Route::get('/session', function () {
        return ['status' => 'API is running'];
    });

    Route::post('/signin', function () {
        return ['status' => 'API is running'];
    });

    Route::post('/refresh', function () {
        return ['status' => 'API is running'];
    });

    Route::post('/signout', function () {
        return ['status' => 'API is running'];
    });
});
