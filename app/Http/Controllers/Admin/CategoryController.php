<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Inertia\Response;
use App\Services\CategoryService;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        if (request()->user()->cannot('viewAny', Category::class)) {
            abort(403);
        }
        return Inertia::render('admin/categories/Index', [
            'categories' => CategoryResource::collection($this->categoryService->getCategories()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        if (request()->user()->cannot('create', Category::class)) {
            abort(403);
        }
        return Inertia::render('admin/categories/Create', [
            'can' => [
                'create' => Auth::user()?->can('create', Category::class),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request, string|null $locale): RedirectResponse
    {
        $this->categoryService->createCategory($request->validated());

        return redirect()->route('admin.categories.index', [
            'locale' => $locale ?? app()->getLocale(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string|null $locale, Category $category): Response
    {
        if (request()->user()->cannot('update', $category)) {
            abort(403);
        }
        return Inertia::render('admin/categories/Edit', [
            'can' => [
                'edit' => Auth::user()?->can('edit', Category::class),
            ],
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, string|null $locale, Category $category): RedirectResponse
    {
        $this->categoryService->updateCategory($category, $request->validated());

        return redirect()->route('admin.categories.index', [
            'locale' => $locale ?? app()->getLocale(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string|null $locale, Category $category): RedirectResponse
    {
        if (request()->user()->cannot('delete', $category)) {
            abort(403);
        }
        $this->categoryService->deleteCategory($category);

        return redirect()->route('admin.categories.index', [
            'locale' => $locale ?? app()->getLocale(),
        ]);
    }
}
