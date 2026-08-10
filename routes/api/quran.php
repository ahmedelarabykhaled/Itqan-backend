<?php

use App\Http\Controllers\Api\QuranRecitationController;
use Illuminate\Support\Facades\Route;

Route::get('recitations', [QuranRecitationController::class, 'index']);
Route::get('recitations/{slug}', [QuranRecitationController::class, 'show']);
Route::get('recitations/{slug}/ayahs', [QuranRecitationController::class, 'ayahs']);
Route::get('recitations/{slug}/ayahs/{surah}/{ayah}', [QuranRecitationController::class, 'ayah']);
