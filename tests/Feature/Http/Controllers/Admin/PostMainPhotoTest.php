<?php

use App\Models\User;
use App\Models\Post;
use App\Models\Language;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('admin can create post with main photo', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $language = Language::first();
    $file = UploadedFile::fake()->image('main.jpg');
    $data = [
        'title' => 'Test Post',
        'slug' => 'test-post-main-photo',
        'content_md' => 'Test content',
        'language_id' => $language->id,
        'status' => 'published',
        'published_at' => now(),
        'main_photo' => $file,
    ];
    $response = $this->post(route('admin.posts.store', ['locale' => $locale]), $data);
    $response->assertRedirect();
    $post = Post::where('slug', 'test-post-main-photo')->first();
    expect($post)->not()->toBeNull();
    expect($post->mainPhoto)->not()->toBeNull();
    Storage::disk('public')->assertExists($post->mainPhoto->file_path);
});

test('admin can update post main photo', function () {
    Storage::fake('public');
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $post = Post::factory()->create();
    $file = UploadedFile::fake()->image('updated.jpg');
    $data = [
        'title' => $post->title,
        'slug' => $post->slug,
        'content_md' => $post->content_md,
        'language_id' => $post->language_id,
        'status' => $post->status,
        'published_at' => $post->published_at->format('Y-m-d H:i:s'),
        'main_photo' => $file,
    ];
    $response = $this->put(route('admin.posts.update', ['post' => $post, 'locale' => $locale]), $data);

    $response->assertRedirect();
    $post->refresh();
    expect($post->mainPhoto)->not()->toBeNull();
    expect($post->mainPhoto->file_name)->toBe('updated.jpg');
    Storage::disk('public')->assertExists($post->mainPhoto->file_path);
});
