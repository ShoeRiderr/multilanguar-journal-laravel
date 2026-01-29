<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StorePageRequest;
use App\Http\Requests\UpdatePageRequest;
use App\Models\Page;
use App\Http\Resources\PageResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\PageService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;

class PageController extends Controller
{
    private PageService $pageService;

    public function __construct(PageService $pageService)
    {
        $this->pageService = $pageService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        if ($request->user()->cannot('viewAny', Page::class)) {
            abort(403);
        }
        $languageId = $request->header('Language-ID', app()->getLocale());

        return Inertia::render('admin/pages/Index', [
            'pages' => PageResource::collection($this->pageService->getPagesByLanguage($languageId)),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        if (request()->user()->cannot('create', Page::class)) {
            abort(403);
        }
        return Inertia::render('admin/pages/Create', [
            'can' => [
                'create' => Auth::user()?->can('create', Page::class),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePageRequest $request, string $locale): RedirectResponse
    {
        $this->pageService->createPage($request->validated());

        return redirect()->route('admin.pages.index', [
            'locale' => $locale ?? app()->getLocale(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $locale, Page $page): Response
    {
        if (request()->user()->cannot('update', $page)) {
            abort(403);
        }
        return Inertia::render('admin/pages/Edit', [
            'can' => [
                'edit' => Auth::user()?->can('update', $page),
            ],
            'page' => $page,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePageRequest $request, string $locale, Page $page): RedirectResponse
    {
        $this->pageService->updatePage($page, $request->validated());

        return redirect()->route('admin.pages.index', [
            'locale' => $locale ?? app()->getLocale(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $locale, Page $page): RedirectResponse
    {
        if (request()->user()->cannot('delete', $page)) {
            abort(403);
        }
        $this->pageService->deletePage($page);

        return redirect()->route('admin.pages.index', [
            'locale' => $locale ?? app()->getLocale(),
        ]);
    }
}
