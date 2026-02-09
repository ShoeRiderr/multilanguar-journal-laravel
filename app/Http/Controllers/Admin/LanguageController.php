<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Models\Language;
use App\Http\Resources\LanguageResource;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\LanguageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class LanguageController extends Controller
{
    private LanguageService $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        if (request()->user()->cannot('viewAny', Language::class)) {
            abort(403);
        }
        return Inertia::render('admin/languages/Index', [
            'language_list' => LanguageResource::collection($this->languageService->getLanguages()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        if (request()->user()->cannot('create', Language::class)) {
            abort(403);
        }
        return Inertia::render('admin/languages/Create', [
            'can' => [
                'create' => Auth::user()?->can('create', Language::class),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLanguageRequest $request, string|null $locale): RedirectResponse
    {
        $this->languageService->createLanguage($request->validated());

        return redirect()->route('admin.languages.index', [
            'locale' => app()->getLocale(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string|null $locale, Language $language): Response
    {
        if (request()->user()->cannot('update', $language)) {
            abort(403);
        }
        return Inertia::render('admin/languages/Edit', [
            'can' => [
                'edit' => Auth::user()?->can('update', Language::class),
            ],
            'language' => $language,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLanguageRequest $request, string|null $locale, Language $language): RedirectResponse
    {
        $this->languageService->updateLanguage($language, $request->validated());

        return redirect()->route('admin.languages.index', [
            'locale' => app()->getLocale(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|null $locale, Language $language): RedirectResponse
    {
        if (request()->user()->cannot('delete', $language)) {
            abort(403);
        }
        $this->languageService->deleteLanguage($language);

        return redirect()->route('admin.languages.index', [
            'locale' => $locale ?? app()->getLocale(),
        ]);
    }
}
