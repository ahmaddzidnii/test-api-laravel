<?php

use App\Http\Controllers\AuthController;
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
    'controller' => AuthController::class,
], function () {
    Route::get('/', 'listProjects');
    Route::get('/{projectId}', 'getProjectById');
    Route::get('/{slug}', 'getProjectBySlug');
    Route::get('/tech-stacks/lists', 'listTechStacks');

    Route::middleware(['jwt.extract', 'jwt.required'])->group(function () {
        Route::post('/', 'createProject');
        Route::patch('/change-slug/{id}', 'changeProjectSlug');
        Route::delete('/{projectId}', 'deleteProject');
        Route::patch('/{projectId}/basic-info', 'updateProjectBasicInfo');
        Route::post('/{projectId}/technologies', 'syncProjectTechnologies');
        Route::post('/{projectId}/details', 'syncProjectDetails');
        Route::post('/{projectId}/testimonial', 'createProjectTestimonial');
        Route::delete('/{projectId}/testimonial/{testimonialId}', 'deleteProjectTestimonial');
        Route::patch('/{projectId}/testimonial/{testimonialId}', 'updateProjectTestimonial');
    });
});
