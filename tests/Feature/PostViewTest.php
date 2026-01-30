<?php

use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use App\Models\Category;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

test('deleting a post removes related category_post and post_views records', function () {
    $post = Post::factory()->create();
    $category = Category::factory()->create();
    $post->categories()->attach($category);
    $postView = PostView::create([
        'post_id' => $post->id,
        'view_count' => 3,
        'last_viewed_at' => now(),
    ]);
    $this->assertDatabaseHas('category_post', [
        'post_id' => $post->id,
        'category_id' => $category->id,
    ]);
    $this->assertDatabaseHas('post_views', [
        'post_id' => $post->id,
    ]);
    $post->delete();
    $this->assertDatabaseMissing('category_post', [
        'post_id' => $post->id,
        'category_id' => $category->id,
    ]);
    $this->assertDatabaseMissing('post_views', [
        'post_id' => $post->id,
    ]);
});

test('viewing a post increments view_count only after 5 minutes', function () {
    $post = Post::factory()->has(PostView::factory()->state([
        'view_count' => 0,
        'last_viewed_at' => now()->subHour(),
    ]))->create();
    $locale = app()->getLocale();
    // First view: should create PostView and set count to 1
    $response = $this->get(route('posts.view', ['locale' => $locale, 'post' => $post->id]));
    $response->assertJson(['success' => true, 'view_count' => 1]);
    $this->assertDatabaseHas('post_views', [
        'post_id' => $post->id,
        'view_count' => 1,
    ]);
    // Second view within 5 minutes: should NOT increment
    $response = $this->get(route('posts.view', ['locale' => $locale, 'post' => $post->id]));
    $response->assertJson(['success' => true, 'view_count' => 1]);
    // Fast-forward 6 minutes
    $postView = PostView::where('post_id', $post->id)->first();
    $postView->last_viewed_at = Carbon::now()->subMinutes(6);
    $postView->save();
    // Third view after 5 minutes: should increment
    $response = $this->get(route('posts.view', ['locale' => $locale, 'post' => $post->id]));
    $response->assertJson(['success' => true, 'view_count' => 2]);
    $this->assertDatabaseHas('post_views', [
        'post_id' => $post->id,
        'view_count' => 2,
    ]);
});
