<?php

namespace App\Providers;

use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use App\Http\Responses\LogoutResponse;
use Laravel\Fortify\Contracts\EmailVerificationNotificationSentResponse as EmailVerificationNotificationSentResponseContract;
use App\Http\Responses\EmailVerificationNotificationSentResponse;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LoginResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Override Fortify's LogoutResponse binding
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
        // Override Fortify's EmailVerificationNotificationSentResponse binding
        $this->app->singleton(EmailVerificationNotificationSentResponseContract::class, EmailVerificationNotificationSentResponse::class);
        $this->app->singleton(
            LoginResponseContract::class,
            LoginResponse::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        \App\Models\Post::observe(\App\Observers\PostObserver::class);

        \Inertia\Inertia::share('pages', function () {
            $route = \Illuminate\Support\Facades\Route::current();
            $middlewares = $route ? $route->gatherMiddleware() : [];

            // Only share if not using 'auth' or 'verified'
            if (in_array('auth', $middlewares) || in_array('verified', $middlewares)) {
                return [];
            }

            $locale = app()->getLocale();
            $language = \App\Models\Language::where('code', $locale)->first();
            if (!$language) {
                return [];
            }

            $user = auth()->user();

            // If user is not logged in or not admin, show only active pages
            $query = \App\Models\Page::query();
            if (!$user || !$user->isAdmin()) {
                $query->where('is_active', true);
            }

            $pages = $query->whereHas('pageTranslations', function ($q) use ($language) {
                    $q->where('language_id', $language->id);
                })
                ->with(['pageTranslations' => function ($q) use ($language) {
                    $q->where('language_id', $language->id);
                }])
                ->get()
                ->map(function ($page) {
                    $t = $page->pageTranslations->first();
                    return [
                        'id' => $t->id,
                        'title' => $t->title,
                        'slug' => $t->slug,
                        'is_active' => $page->is_active,
                    ];
                })
                ->values();

            return $pages;
        });
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );
        $this->app->bind(
            \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class,
            \App\Http\Middleware\RedirectIfAuthenticated::class
        );
        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}
