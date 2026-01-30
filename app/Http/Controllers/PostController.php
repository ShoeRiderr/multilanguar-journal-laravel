<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Post;
use App\Http\Resources\PostResource;
use App\Services\PostService;

class PostController extends Controller
{
    protected $postService;

    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        if (request()->user()->cannot('viewAny', Post::class)) {
            abort(403);
        }
        $languageId = $request->header('Language-ID', 'en');
        $posts = $this->postService->getPostsByLanguage($languageId);
        return Inertia::render('posts/Index', [
            'posts' => PostResource::collection($posts),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $locale, Post $post): Response
    {
        if (request()->user()->cannot('view', $post)) {
            abort(403);
        }
        $post->load(['categories', 'postView']);
        return Inertia::render('posts/Show', [
            'post' => new PostResource($post),
        ]);
    }
}
