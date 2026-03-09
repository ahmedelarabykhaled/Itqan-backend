<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;

Route::get('countries', [LocationController::class, 'getCountries']);
Route::get('countries/{country_id}/cities', [LocationController::class, 'getCitiesByCountryId']);