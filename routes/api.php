<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomersAuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('customers')->group(function () {
    Route::post('auth/login', [CustomersAuthController::class, 'login']);
    Route::post('auth/register', [CustomersAuthController::class, 'register']);
    Route::post('auth/logout', [CustomersAuthController::class, 'logout']);
    Route::post('auth/social-login', [CustomersAuthController::class, 'socialLogin']);
    Route::post('auth/social-register', [CustomersAuthController::class, 'socialRegister']);
    Route::post('auth/forgot-password', [CustomersAuthController::class, 'forgotPassword']);
    Route::post('auth/reset-password', [CustomersAuthController::class, 'resetPassword']);
    Route::post('auth/verify-code', [CustomersAuthController::class, 'verifyCode']);
    Route::put('auth/update', [CustomersAuthController::class, 'update'])->middleware('auth:customers');
});
