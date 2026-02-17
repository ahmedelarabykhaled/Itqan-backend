<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Customers\CustomersController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('profile', [CustomersController::class, 'getProfile']);
});