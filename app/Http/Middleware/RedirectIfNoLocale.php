<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNoLocale
{
    /**
     * List of routes that require a locale prefix
     * e.g., Fortify routes
     */
    protected $localeRoutes = [
        '/',
        'login',
        'register',
        'forgot-password',
        'reset-password',
        'email/verify',
        'dashboard',
        'categories',
    ];

    public function handle(Request $request, Closure $next)
    {
        $languageId = $request->header('Language-ID', 'en');
        $segments = $request->segments();

        // Get first segment
        $first = $segments[0] ?? null;

        // If first segment is NOT a locale AND path matches a defined route
        if (
            (!preg_match('/^[a-z]{2}$/', $first)) && 
            in_array($request->path(), $this->localeRoutes)
        ) {
            return redirect()->route(
                $this->getRouteName($request->path()),
                ['locale' => $languageId]
            );
        }

        return $next($request);
    }

    private function getRouteName(string $path): string
    {
        return match ($path) {
            '/' => 'home',
            'login' => 'login',
            'register' => 'register',
            'forgot-password' => 'password.request',
            'reset-password' => 'password.reset',
            'email/verify' => 'verification.notice',
            'dashboard' => 'dashboard',
            'categories' => 'categories.index',
            default => 'home',
        };
    }
}
