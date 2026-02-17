<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomersAuthController;

Route::post('login', [CustomersAuthController::class, 'login']);
Route::post('register', [CustomersAuthController::class, 'register']);
Route::post('social-login', [CustomersAuthController::class, 'socialLogin']);
Route::post('social-register', [CustomersAuthController::class, 'socialRegister']);
Route::post('forgot-password', [CustomersAuthController::class, 'forgotPassword']);
Route::post('reset-password', [CustomersAuthController::class, 'resetPassword'])->name('password.reset');
Route::post('validate-code', [CustomersAuthController::class, 'validateCode']);

// protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [CustomersAuthController::class, 'logout']);
    Route::put('update', [CustomersAuthController::class, 'update']);
});