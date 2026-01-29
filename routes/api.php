<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
    Route::delete('/{userId}', 'deleteUser');
});

Route::group([
    'prefix' => 'projects',
], function () {
    Route::get('/', [ProjectController::class, 'listProjects']);
    Route::get('/{projectId}', [ProjectController::class, 'getProjectByIdOrSlug']);
    Route::middleware(['jwt.extract', 'jwt.required'])->group(function () {
        Route::post('/', [ProjectController::class, 'createProject']);
        Route::delete('/{projectId}', [ProjectController::class, 'deleteProject']);
        Route::patch('/change-slug/{id}', [ProjectController::class, 'changeProjectSlug']);

        Route::patch('/{projectId}/basic-info', [ProjectController::class, 'updateProjectBasicInfo']);
        Route::post('/{projectId}/technologies', [ProjectController::class, 'syncProjectTechnologies']);
        Route::post('/{projectId}/details', [ProjectController::class, 'syncProjectDetails']);

        Route::post('/{projectId}/testimonial', [ProjectController::class, 'createProjectTestimonial']);
        Route::delete('/{projectId}/testimonial/{testimonialId}', [ProjectController::class, 'deleteProjectTestimonial']);
        Route::patch('/{projectId}/testimonial/{testimonialId}', [ProjectController::class, 'updateProjectTestimonial']);

        Route::post('/{projectId}/images/upload', [ProjectController::class, 'uploadProjectImage']);
        Route::patch('/{projectId}/images/{imageId}', [ProjectController::class, 'setPrimaryProjectImage']);
        Route::delete('/{projectId}/images/{imageId}', [ProjectController::class, 'deleteProjectImage']);
    });
});

Route::group([
    'prefix' => 'technologies',
], function () {
    Route::get('/', [App\Http\Controllers\TechnologyController::class, 'index']);
});
