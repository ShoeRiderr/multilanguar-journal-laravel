<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;
use App\Http\Resources\PostResource;

class WelcomeController extends Controller
{
    private PostService $postService;
    
    public function __construct(PostService $postService)
    {
        $this->postService = $postService;
    }

    public function index(string $locale): Response
    {
        $posts = $this->postService->getPostsByLanguage($locale, 10, 3);
        return Inertia::render('Welcome', [
            'canRegister' => Features::enabled(Features::registration()),
            'posts' => PostResource::collection($posts),
        ]);
    }
}
