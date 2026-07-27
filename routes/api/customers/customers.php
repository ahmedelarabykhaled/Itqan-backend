<?php

use App\Http\Controllers\Api\Customers\CustomersController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('profile', [CustomersController::class, 'getProfile']);
    Route::delete('profile', [CustomersController::class, 'deleteAccount']);
});
