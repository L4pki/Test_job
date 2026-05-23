<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)
    ->prefix('/user')->group(function () {
    Route::post('/create', 'registrationUser');
    Route::post('/login', 'authorizationUser');
    Route::post('/refresh', 'refreshToken')
        ->middleware('auth:sanctum');
    });

Route::controller(PostController::class)
    ->middleware('auth:sanctum')
    ->prefix('/post')->group(function () {
        Route::post('/create', 'publicationPost');
        Route::prefix('/get')->group(function () {
            Route::get('/all', 'getPosts');
            Route::get('/user', 'getUserPosts');
        });
    });

