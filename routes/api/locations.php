<?php

use App\Http\Controllers\Api\LocationController;
use Illuminate\Support\Facades\Route;

Route::get('countries', [LocationController::class, 'getCountries']);
Route::get('countries/{country_id}/cities', [LocationController::class, 'getCitiesByCountryId']);
