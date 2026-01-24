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
        $locale = $request->route('locale', env('APP_LOCALE', 'en'));
        
        if ($locale) {
            $language = Language::where('code', $locale)->firstOrFail();
            $request->headers->set('Language-ID', $language->id);
        } else {
            // Set default language if no locale is provided
            $defaultLanguage = Language::where('is_default', true)->first();
            if ($defaultLanguage) {
                $request->headers->set('Language-ID', $defaultLanguage->id);
            }
        }
        
        return $next($request);
    }
}
