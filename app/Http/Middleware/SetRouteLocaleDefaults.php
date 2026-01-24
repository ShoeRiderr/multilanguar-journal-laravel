<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetRouteLocaleDefaults
{
    public function handle(Request $request, Closure $next)
    {
        // 1) preferujemy locale z trasy (prefix), potem header Language-ID, potem aplikacja
        $locale = $request->route('locale')
            ?? $request->header('Language-ID')
            ?? app()->getLocale();

        // 2) normalizacja (np. 'pl' lub 'en')
        if (!is_string($locale) || $locale === '') {
            $locale = app()->getLocale() ?: 'en';
        }
        $locale = strtolower(substr($locale, 0, 2));

        // 3) ustaw domyślny parametr 'locale' dla generatora URL
        URL::defaults(['locale' => $locale]);

        // 4) ustaw locale aplikacji (opcjonalne, ale przydatne)
        app()->setLocale($locale);

        return $next($request);
    }
}