<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * Obsługuje zarówno pojedynczy guard (starsze wersje), jak i wielo-guardową sygnaturę.
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Jeśli sygnatura wywołana z pojedynczym $guard (np. handle($req, $next, $guard = null))
        if (count($guards) === 1 && $guards[0] === null) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $locale = $request->route('locale') ?? app()->getLocale();
                if ($user && $user->isAdmin()) {
                    $fallback = route('dashboard', ['locale' => $locale]);
                } else {
                    $fallback = route('home', ['locale' => $locale]);
                }
                if ($request->header('X-Inertia')) {
                    return Inertia::location($fallback);
                }
                return redirect()->intended($fallback);
            }
        }

        return $next($request);
    }
}