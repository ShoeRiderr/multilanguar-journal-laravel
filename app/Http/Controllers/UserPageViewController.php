<?php

namespace App\Http\Controllers;

use App\Http\Resources\PageResource;
use Inertia\Inertia;
use App\Models\Page;

class UserPageViewController extends Controller
{
    /**
     * Display the specified page translation by slug and locale code using Inertia.
     *
     * @param  string  $locale
     * @param  string  $slug
     * @return \Inertia\Response
     */
    public function show($locale, $slug)
    {
        $page = Page::whereHas('pageTranslations', function ($query) use ($slug, $locale) {
                $query->where('slug', $slug)
                      ->whereHas('language', function ($q) use ($locale) {
                          $q->where('code', $locale);
                      });
            })
            ->with(['pageTranslations' => function ($query) use ($slug, $locale) {
                $query->where('slug', $slug)
                      ->whereHas('language', function ($q) use ($locale) {
                          $q->where('code', $locale);
                      });
            }])
            ->first();

        if (!$page || $page->pageTranslations->isEmpty()) {
            abort(404, 'Page not found');
        }

        return Inertia::render('UserPageView', [
            'page' => new PageResource($page),
        ]);
    }
}
