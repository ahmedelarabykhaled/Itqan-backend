<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Customers\AuthController;
use App\Http\Controllers\Api\Customers\SocialAuthController;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('social-login', [SocialAuthController::class, 'socialLogin']);
Route::post('social-register', [SocialAuthController::class, 'socialRegister']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('activate-account', [AuthController::class, 'activateAccount']);
Route::put('update', [AuthController::class, 'update']);
// protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('update', [AuthController::class, 'update']);
});