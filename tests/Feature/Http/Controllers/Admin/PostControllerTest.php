<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Language;
use App\Models\PostView;
use App\Models\Category;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

test('admin can create post with translations', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $language = Language::first();
    $categories = Category::factory()->count(2)->create();
    $data = [
        'title' => 'Test Post',
        'slug' => 'test-post',
        'content_md' => 'Test content',
        'language_id' => $language->id,
        'status' => 'published',
        'published_at' => now(),
        'categories' => $categories->pluck('id')->all(),
    ];
    $response = $this->post(route('admin.posts.store', ['locale' => $locale]), $data);
    $response->assertRedirect();
    $postData = $data;
    unset($postData['categories']);
    $this->assertDatabaseHas('posts', $postData);
    $createdPost = Post::where('slug', 'test-post')->first();
    foreach ($categories as $category) {
        $this->assertDatabaseHas('category_post', [
            'post_id' => $createdPost->id,
            'category_id' => $category->id,
        ]);
    }
});

test('denies access to non-admin users', function () {
    $user = User::factory()->create(['role' => UserRole::USER]);
    $post = Post::factory()->create();
    $locale = app()->getLocale();
    $routes = [
        'get' => [
            route('admin.posts.index', ['locale' => $locale]),
            route('admin.posts.create', ['locale' => $locale]),
            route('admin.posts.edit', ['post' => $post, 'locale' => $locale]),
        ],
        'post' => [
            route('admin.posts.store', ['locale' => $locale]),
        ],
        'put' => [
            route('admin.posts.update', ['post' => $post, 'locale' => $locale]),
        ],
        'delete' => [
            route('admin.posts.destroy', ['post' => $post, 'locale' => $locale]),
        ],
    ];
    $this->actingAs($user);
    foreach ($routes['get'] as $route) {
        $this->get($route)->assertForbidden();
    }
    foreach ($routes['post'] as $route) {
        $this->post($route, [])->assertForbidden();
    }
    foreach ($routes['put'] as $route) {
        $this->put($route, [])->assertForbidden();
    }
    foreach ($routes['delete'] as $route) {
        $this->delete($route)->assertForbidden();
    }
});

test('allows access to admin users', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $post = Post::factory()->create();
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $this->get(route('admin.posts.index', ['locale' => $locale]))->assertOk();
    $this->get(route('admin.posts.create', ['locale' => $locale]))->assertOk();
    $this->get(route('admin.posts.edit', ['post' => $post, 'locale' => $locale]))->assertOk();
});

test('admin can update post with translations', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $post = Post::factory()->create();
    $categories = Category::factory()->count(2)->create();
    $data = [
        'title' => 'Updated Post',
        'slug' => 'updated-post',
        'content_md' => 'Updated content',
        'language_id' => $post->language_id,
        'status' => 'published',
        'published_at' => now(),
        'categories' => $categories->pluck('id')->all(),
    ];
    $response = $this->put(route('admin.posts.update', ['post' => $post, 'locale' => $locale]), $data);
    $response->assertRedirect();
    $postData = $data;
    unset($postData['categories']);
    $this->assertDatabaseHas('posts', $postData);
    foreach ($categories as $category) {
        $this->assertDatabaseHas('category_post', [
            'post_id' => $post->id,
            'category_id' => $category->id,
        ]);
    }
});

test('admin can delete a post', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $post = Post::factory()->create();
    $this->delete(route('admin.posts.destroy', ['post' => $post, 'locale' => $locale]))->assertRedirect();
});

test('index loads categories and postView relations', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $language = Language::first();
    $post = Post::factory()->has(PostView::factory()->state([
        'view_count' => 5,
        'last_viewed_at' => now(),
    ])->count(1))->create(['language_id' => $language->id])
        ;
    $category = \App\Models\Category::factory()->create();
    $post->categories()->attach($category);
    $response = $this->get(route('admin.posts.index', ['locale' => $locale]));
    $response->assertOk();
    $responseData = $response->original->getData();
    $posts = $responseData['page']['posts'] ?? $responseData['page']['props']['posts'] ?? null;
    expect($posts)->not()->toBeNull();
    $first = $posts[0] ?? $posts['data'][0] ?? null;
    expect($first)->not()->toBeNull();
    // Check categories and postView loaded
    expect(isset($first['categories']) || isset($first->categories))->toBeTrue();
    expect(isset($first['post_view']) || isset($first->postView))->toBeTrue();
});
