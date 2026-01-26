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
        return Inertia::render('admin/languages/Index', [
            'languages' => LanguageResource::collection($this->languageService->getLanguages()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('admin/languages/Create', [
            'can' => [
                'create' => Auth::user()?->can('create', Language::class),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLanguageRequest $request): RedirectResponse
    {
        $this->languageService->createLanguage($request->validated());

        return redirect()->route('admin.languages.index', [
            'locale' => app()->getLocale(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language): Response
    {
        return Inertia::render('admin/languages/Edit', [
            'can' => [
                'edit' => Auth::user()?->can('edit', Language::class),
            ],
            'language' => $language,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLanguageRequest $request, Language $language): RedirectResponse
    {
        $this->languageService->updateLanguage($language, $request->validated());

        return redirect()->route('admin.languages.index', [
            'locale' => app()->getLocale(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language): RedirectResponse
    {
        $this->languageService->deleteLanguage($language);

        return redirect()->route('admin.languages.index', [
            'locale' => app()->getLocale(),
        ]);
    }
}
