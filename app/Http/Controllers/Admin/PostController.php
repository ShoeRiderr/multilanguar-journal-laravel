<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

class PostController extends Controller
{
    private PostService $postService;

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

        return Inertia::render('admin/posts/Index', [
            'posts' => PostResource::collection($this->postService->getPostsByLanguage($languageId)),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        if (request()->user()->cannot('create', Post::class)) {
            abort(403);
        }
        return Inertia::render('admin/posts/Create', [
            'can' => [
                'create' => Auth::user()?->can('create', Post::class),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->postService->createPost($request->validated());

        return redirect()->route('admin.posts.index', [
            'locale' => app()->getLocale(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): Response
    {
        if (request()->user()->cannot('update', $post)) {
            abort(403);
        }
        return Inertia::render('admin/posts/Edit', [
            'can' => [
                'edit' => Auth::user()?->can('update', $post),
            ],
            'post' => $post,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->postService->updatePost($post, $request->validated());

        return redirect()->route('admin.posts.index', [
            'locale' => app()->getLocale(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        if (request()->user()->cannot('delete', $post)) {
            abort(403);
        }
        $this->postService->deletePost($post);

        return redirect()->route('admin.posts.index', [
            'locale' => app()->getLocale(),
        ]);
    }
}
