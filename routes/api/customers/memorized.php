<?php

use App\Http\Controllers\Api\Customers\MemorizedAyahController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('memorized', [MemorizedAyahController::class, 'store']);
    Route::get('memorized', [MemorizedAyahController::class, 'index']);
    Route::get('memorized/last', [MemorizedAyahController::class, 'last']);
    Route::get('memorized/summary', [MemorizedAyahController::class, 'summary']);
});
