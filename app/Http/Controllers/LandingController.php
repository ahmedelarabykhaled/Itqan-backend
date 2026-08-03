<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    public function __invoke(Request $request): View
    {
        $locale = $request->query('lang');

        if (! in_array($locale, config('app.supported_locales', ['en']), true)) {
            app()->setLocale('ar');
        }

        return view('landing');
    }
}
