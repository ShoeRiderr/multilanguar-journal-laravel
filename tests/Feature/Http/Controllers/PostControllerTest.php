<?php

use App\Models\Language;
use App\Models\Post;
use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('show loads categories and postView relations', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $language = Language::first();
    $post = Post::factory()->create(['language_id' => $language->id]);
    $category = \App\Models\Category::factory()->create();
    $post->categories()->attach($category);
    $postView = \App\Models\PostView::create([
        'post_id' => $post->id,
        'view_count' => 10,
        'last_viewed_at' => now(),
    ]);
    $response = $this->get(route('posts.show', ['post' => $post, 'locale' => $locale]));
    $response->assertOk();
    $responseData = $response->original->getData();
    $postData = $responseData['page']['post']['data'] ?? $responseData['page']['props']['post']['data'] ?? null;
    expect($postData)->not()->toBeNull();
    expect(isset($postData['categories']) || isset($postData->categories))->toBeTrue();
    expect(isset($postData['post_view']) || isset($postData->postView))->toBeTrue();
});