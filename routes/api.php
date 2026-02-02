<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GalleryAdminController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\ProjectAdminController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'apiVersion' => '1.0',
        'apiDescription' => 'SCIT Website API',
        'serverTime' => now()->toDateTimeString(),
    ]);
});

Route::group([
    'prefix' => 'auth',
    'controller' => AuthController::class,
], function () {
    Route::post('/signin', 'login');
    Route::post('/refresh', 'refreshToken');

    Route::middleware(['jwt.extract', 'jwt.required'])->group(function () {
        Route::get('/session', 'getUserInfo');
        Route::post('/signout', 'logout');
    });
});

Route::group([
    'prefix' => 'users',
    'middleware' => ['jwt.extract', 'jwt.required'],
    'controller' => UserController::class,
], function () {
    Route::get('/', 'getUserInfo');
    Route::patch('/', 'updateUserInfo');
    Route::post('/', 'createUser');
    Route::delete('/{userId}', 'deleteUser')->where('userId', '[0-9]+');
});

Route::group([
    'prefix' => 'projects',
], function () {
    Route::get('/', [ProjectController::class, 'listProjects']);
    Route::get('/{projectId}', [ProjectController::class, 'getProjectByIdOrSlug'])->where('projectId', '[0-9a-zA-Z\-]+');
});

Route::group([
    'prefix' => 'galleries',
], function () {
    Route::get('/', GalleryController::class);
});

Route::prefix('admin/projects')
    ->middleware(['jwt.extract', 'jwt.required'])
    ->group(function () {

        Route::get('/', [ProjectAdminController::class, 'listProjects']);
        Route::get('/{projectId}', [ProjectAdminController::class, 'getProjectByIdOrSlug'])->where('projectId', '[0-9a-zA-Z\-]+');

        Route::post('/', [ProjectAdminController::class, 'createProject']);
        Route::delete('/{projectId}', [ProjectAdminController::class, 'deleteProject'])->where('projectId', '[0-9]+');

        Route::patch('/change-slug/{id}', [ProjectAdminController::class, 'changeSlug'])->where('id', '[0-9]+');
        Route::patch('/{projectId}/basic-info', [ProjectAdminController::class, 'updateBasicInfo'])->where('projectId', '[0-9]+');

        Route::post('/{projectId}/technologies', [ProjectAdminController::class, 'syncTechnologies'])->where('projectId', '[0-9]+');
        Route::post('/{projectId}/details', [ProjectAdminController::class, 'syncDetails'])->where('projectId', '[0-9]+');

        Route::post('/{projectId}/testimonials', [ProjectAdminController::class, 'createTestimonial'])->where('projectId', '[0-9]+');
        Route::patch('/{projectId}/testimonials/{testimonialId}', [ProjectAdminController::class, 'updateTestimonial'])->where(['projectId' => '[0-9]+', 'testimonialId' => '[0-9]+']);
        Route::delete('/{projectId}/testimonials/{testimonialId}', [ProjectAdminController::class, 'deleteTestimonial'])->where(['projectId' => '[0-9]+', 'testimonialId' => '[0-9]+']);

        Route::post('/{projectId}/images/upload', [ProjectAdminController::class, 'uploadImage'])->where('projectId', '[0-9]+');
        Route::patch('/{projectId}/images/{imageId}', [ProjectAdminController::class, 'setPrimaryImage'])->where(['projectId' => '[0-9]+', 'imageId' => '[0-9]+']);
        Route::delete('/{projectId}/images/{imageId}', [ProjectAdminController::class, 'deleteImage'])->where(['projectId' => '[0-9]+', 'imageId' => '[0-9]+']);

        Route::post('/visibility', [ProjectAdminController::class, 'updateVisibilityBatch']);
    });

Route::group([
    'prefix' => 'admin/galleries',
    'middleware' => ['jwt.extract', 'jwt.required'],
], function () {
    Route::get('/', [GalleryAdminController::class, 'index']);
    Route::post('/', [GalleryAdminController::class, 'store']);
    Route::delete('/{galleryId}', [GalleryAdminController::class, 'destroy'])->where('galleryId', '[0-9]+');
});

Route::group([
    'prefix' => 'technologies',
], function () {
    Route::get('/', [App\Http\Controllers\TechnologyController::class, 'index']);
});
