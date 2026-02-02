<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Language;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = $request->route('locale', app()->getLocale());
        $file = lang_path($locale . '.json');
        $translations = file_exists($file) ? json_decode(file_get_contents($file), true) : [];

        return array_merge(parent::share($request), [
            'name' => config('app.name'),
            'locale' => $locale,
            'locales' => config('app.available_locales'),
            'languages' => Language::all(['id', 'code', 'name'])->toArray(),
            'translations' => $translations,
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => !$request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ]);
    }
}
