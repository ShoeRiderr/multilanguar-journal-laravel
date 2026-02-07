<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class SetRouteLocaleDefaults
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->route('locale')
            ?? $request->header('Language-ID')
            ?? app()->getLocale();

        if (!is_string($locale) || $locale === '') {
            $locale = app()->getLocale() ?: 'en';
        }
        $locale = strtolower(substr($locale, 0, 2));

        URL::defaults(['locale' => $locale]);

        app()->setLocale($locale);

        return $next($request);
    }
}