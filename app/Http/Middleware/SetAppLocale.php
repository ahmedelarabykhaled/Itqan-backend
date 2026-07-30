<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAppLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang') ?? $request->header('Accept-Language');

        if ($locale) {
            $locale = substr($locale, 0, 2);
        }

        if ($locale && in_array($locale, ['ar', 'en'], true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
