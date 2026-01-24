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
                // Spróbuj pobrać locale z trasy (prefixu), w przeciwnym razie użyj app()->getLocale()
                $locale = $request->route('locale') ?? app()->getLocale();

                // docelowa, zabezpieczona trasa (fallback)
                $fallback = route('dashboard', ['locale' => $locale]);

                // Jeśli to żądanie Inertia, użyj Inertia::location żeby klient SPA prawidłowo przekierował
                if ($request->header('X-Inertia')) {
                    return Inertia::location($fallback);
                }

                // Użyj intended jeśli była zapamiętana intencja, inaczej fallback z locale
                return redirect()->intended($fallback);
            }
        }

        return $next($request);
    }
}