<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)
    ->prefix('/user')->group(function () {
    Route::post('/create', 'registrationUser');
});

Route::post('/post/create', function (Request $request) {
    return $request->post();
})->middleware('auth:sanctum');

Route::controller(PostController::class) ->group(function () {
    Route::get('/posts', 'getPosts');
});

Route::get('/post/user', function (Request $request) {
    return $request->post();
})->middleware('auth:sanctum');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

