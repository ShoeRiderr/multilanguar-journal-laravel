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
        $languageId = $request->header('Language-ID', 'en');
        $posts = $this->postService->getPostsByLanguage($languageId, 4);
        return Inertia::render('posts/Index', [
            'canRegister' => Features::enabled(Features::registration()),
            'posts' => PostResource::collection($posts),
            'categories' => CategoryResource::collection($this->categoryService->getCategories(true)),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $locale, Post $post): Response
    {
        $post->load(['categories', 'postView']);
        return Inertia::render('posts/Show', [
            'post' => new PostResource($post),
        ]);
    }
}
