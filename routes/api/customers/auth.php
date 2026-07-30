<?php

use App\Http\Controllers\Api\Customers\AuthController;
use App\Http\Controllers\Api\Customers\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('social-login', [SocialAuthController::class, 'socialLogin']);
Route::post('social-register', [SocialAuthController::class, 'socialRegister']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('verify-reset-otp', [AuthController::class, 'verifyResetOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('activate-account', [AuthController::class, 'activateAccount']);
Route::post('resend-activation-otp', [AuthController::class, 'resendActivationOtp']);
Route::put('update', [AuthController::class, 'update']);
// protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::put('update', [AuthController::class, 'update']);
});
