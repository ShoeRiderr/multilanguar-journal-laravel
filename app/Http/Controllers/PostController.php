<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Post;
use App\Http\Resources\PostResource;
use App\Services\CategoryService;
use App\Services\LanguageService;
use App\Services\PostService;
use Laravel\Fortify\Features;
use App\Models\Language;

class PostController extends Controller
{
    protected PostService $postService;
    protected CategoryService $categoryService;

    public function __construct(PostService $postService, LanguageService $languageService, CategoryService $categoryService)
    {
        $this->postService = $postService;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $locale = $request->route('locale', app()->getLocale());
        $language = Language::where('code', $locale)->first();
        $defaultLanguageId = $language?->id;

        $filters = [
            'search' => $request->input('search'),
            'categories' => array_values(array_filter(array_map('intval', (array) $request->input('categories', [])))),
            'languages' => array_values(array_filter(array_map('intval', (array) $request->input('languages', [])))),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        $posts = $this->postService->getFilteredPosts($filters, 4, $defaultLanguageId);
        $posts->appends($request->query());
        return Inertia::render('posts/Index', [
            'canRegister' => Features::enabled(Features::registration()),
            'posts' => PostResource::collection($posts),
            'categories' => CategoryResource::collection($this->categoryService->getCategories(true)),
            'filters' => $filters,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $locale, Post $post): Response
    {
        $post->load(['categories', 'postView']);
        return Inertia::render('posts/Show', [
            'canRegister' => Features::enabled(Features::registration()),
            'post' => new PostResource($post),
        ]);
    }
}
