<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * Routes that require authentication using Sanctum middleware.
 */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::resource('users', UserController::class)->middleware('admin');
    Route::resource('/products', ProductController::class);
    Route::resource('/orders', OrderController::class);
    Route::post('auth/logout', [AuthController::class, 'logout']);
});

/**
 * Authentication routes.
 */
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
