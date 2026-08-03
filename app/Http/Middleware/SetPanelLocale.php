<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetPanelLocale
{
    public const SESSION_KEY = 'panel_locale';

    /**
     * Resolve the admin panel locale, remembering the administrator's choice
     * for the rest of the session.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var array<int, string> $supportedLocales */
        $supportedLocales = config('app.supported_locales', ['en']);

        $requestedLocale = $request->query('lang');

        if (is_string($requestedLocale) && in_array($requestedLocale, $supportedLocales, true)) {
            $request->session()->put(self::SESSION_KEY, $requestedLocale);
        }

        $locale = $request->session()->get(self::SESSION_KEY, config('app.panel_locale'));

        if (! in_array($locale, $supportedLocales, true)) {
            $locale = config('app.panel_locale');
        }

        app()->setLocale($locale);

        return $next($request);
    }
}
