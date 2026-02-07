<?php

use App\Models\User;
use App\Models\Page;
use App\Models\Language;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\PageTranslation;

uses(RefreshDatabase::class);

test('guest can only see active pages', function () {
    $language = Language::factory()->create([
        'code' => 'ru',
        'name' => 'Russian',
        'native_name' => 'Русский',
        'is_active' => true,
        'is_default' => false,
    ]);
    $activePage = Page::factory()
        ->has(PageTranslation::factory()->state([
            'language_id' => $language->id,
            'title' => 'Active Page',
            'slug' => 'active-page',
            'content_md' => 'Active content',
        ]), 'pageTranslations')
        ->createQuietly(['is_active' => true]);
    $inactivePage = Page::factory()
        ->has(PageTranslation::factory()->state([
            'language_id' => $language->id,
            'title' => 'Inactive Page',
            'slug' => 'inactive-page',
            'content_md' => 'Inactive content',
        ]), 'pageTranslations')
        ->createQuietly(['is_active' => false]);

    $response = $this->get('/ru');
    $pages = $response->original->getData()['page']['pages'] ?? $response->original->getData()['page']['props']['pages'] ?? null;
    expect($pages)->not()->toBeNull();
    $ids = collect($pages)->pluck('title');
    expect($ids)->toContain('Active Page');
    expect($ids)->not()->toContain('Inactive Page');
});

test('user role can only see active pages', function () {
    $user = User::factory()->create(['role' => UserRole::USER]);
$language = Language::factory()->create([
        'code' => 'ru',
        'name' => 'Russian',
        'native_name' => 'Русский',
        'is_active' => true,
        'is_default' => false,
    ]);
$activePage = Page::factory()
        ->has(PageTranslation::factory()->state([
            'language_id' => $language->id,
            'title' => 'Active Page',
            'slug' => 'active-page',
            'content_md' => 'Active content',
        ]), 'pageTranslations')
        ->createQuietly(['is_active' => true]);
    $inactivePage = Page::factory()
        ->has(PageTranslation::factory()->state([
            'language_id' => $language->id,
            'title' => 'Inactive Page',
            'slug' => 'inactive-page',
            'content_md' => 'Inactive content',
        ]), 'pageTranslations')
        ->createQuietly(['is_active' => false]);

    $this->actingAs($user);
    $response = $this->get('/ru');
    $pages = $response->original->getData()['page']['pages'] ?? $response->original->getData()['page']['props']['pages'] ?? null;
    expect($pages)->not()->toBeNull();
    $ids = collect($pages)->pluck('title');
    expect($ids)->toContain('Active Page');
    expect($ids)->not()->toContain('Inactive Page');
});

test('admin can see both active and inactive pages', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $language = Language::factory()->create([
        'code' => 'ru',
        'name' => 'Russian',
        'native_name' => 'Русский',
        'is_active' => true,
        'is_default' => false,
    ]);
    $activePage = Page::factory()
        ->has(PageTranslation::factory()->state([
            'language_id' => $language->id,
            'title' => 'Active Page',
            'slug' => 'active-page',
            'content_md' => 'Active content',
        ]), 'pageTranslations')
        ->create(['is_active' => true]);
    $inactivePage = Page::factory()
        ->has(PageTranslation::factory()->state([
            'language_id' => $language->id,
            'title' => 'Inactive Page',
            'slug' => 'inactive-page',
            'content_md' => 'Inactive content',
        ]), 'pageTranslations')
        ->create(['is_active' => false]);

    $this->actingAs($admin);
    $response = $this->get(route('home', ['locale' => 'ru']));
    $pages = $response->original->getData()['page']['pages'] ?? $response->original->getData()['page']['props']['pages'] ?? null;

    expect($pages)->not()->toBeNull();
    $ids = collect($pages)->pluck('title');
    expect($ids)->toContain('Active Page');
    expect($ids)->toContain('Inactive Page');
});
