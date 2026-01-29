<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Language;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route(
            'locale',
            env(
                'APP_LOCALE',
                Language::where('is_default', true)->first()->code ?? app()->getLocale()
            )
        );
        
        $language = Language::where('code', $locale)->firstOrFail();
        $request->headers->set('Language-ID', $language->id);
        
        return $next($request);
    }
}
