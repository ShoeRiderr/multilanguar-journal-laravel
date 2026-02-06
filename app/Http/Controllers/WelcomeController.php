<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;
use App\Http\Resources\PostResource;
use App\Models\Language;
use App\Models\Post;
use App\PostStatus;

class WelcomeController extends Controller
{
    private PostService $postService;
    
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(string $locale): Response
    {
        $language = Language::where('code', $locale)->firstOrFail();
        $posts = $this->postService->getPostsByLanguage($language->id, 10, 3, PostStatus::PUBLISHED);

        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'posts' => PostResource::collection($posts),
        ]);
    }
}
