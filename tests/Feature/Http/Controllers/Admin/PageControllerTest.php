<?php

use App\Models\User;
use App\Models\Page;
use App\Models\Language;
use App\Models\PageTranslation;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create page with translations', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $languages = Language::all();
    $translations = [];
    foreach ($languages as $lang) {
        $translations[$lang->id] = [
            'title' => 'Page ' . $lang->code,
            'slug' => 'page-slug-' . $lang->code,
            'content_md' => 'Content for ' . $lang->code,
            'language_id' => $lang->id,
        ];
    }
    $response = $this->post(route('admin.pages.store', ['locale' => $locale]), [
        'is_active' => true,
        'translations' => $translations,
    ]);
    $response->assertRedirect();
    $page = Page::all()->last();
    foreach ($languages as $lang) {
        $this->assertDatabaseHas('page_translations', [
            'page_id' => $page->id,
            'language_id' => $lang->id,
            'title' => 'Page ' . $lang->code,
            'slug' => 'page-slug-' . $lang->code,
            'content_md' => 'Content for ' . $lang->code,
        ]);
    }
});

test('denies access to non-admin users', function () {
    $user = User::factory()->create(['role' => UserRole::USER]);
    $page = Page::factory()->create();
    $locale = app()->getLocale();
    $routes = [
        'get' => [
            route('admin.pages.index', ['locale' => $locale]),
            route('admin.pages.create', ['locale' => $locale]),
            route('admin.pages.edit', ['page' => $page, 'locale' => $locale]),
        ],
        'post' => [
            route('admin.pages.store', ['locale' => $locale]),
        ],
        'put' => [
            route('admin.pages.update', ['page' => $page, 'locale' => $locale]),
        ],
        'delete' => [
            route('admin.pages.destroy', ['page' => $page, 'locale' => $locale]),
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
    $page = Page::factory()->create();
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $this->get(route('admin.pages.index', ['locale' => $locale]))->assertOk();
    $this->get(route('admin.pages.create', ['locale' => $locale]))->assertOk();
    $this->get(route('admin.pages.edit', ['page' => $page, 'locale' => $locale]))->assertOk();
});

test('admin can update page with translations', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $languages = Language::get();
    $page = Page::factory()->create();
    $translations = [];
    foreach ($languages as $lang) {
        $translations[$lang->id] = [
            'title' => 'Updated ' . $lang->code,
            'slug' => 'updated-slug-' . $lang->code,
            'content_md' => 'Updated content for ' . $lang->code,
            'language_id' => $lang->id,
        ];
    }
    $response = $this->put("/$locale/admin/pages/{$page->id}", [
        'translations' => $translations,
        'is_active' => true,
    ]);
    $response->assertRedirect();
    foreach ($languages as $lang) {
        $this->assertDatabaseHas('page_translations', [
            'page_id' => $page->id,
            'language_id' => $lang->id,
            'title' => 'Updated ' . $lang->code,
            'slug' => 'updated-slug-' . $lang->code,
        ]);
    }
});

test('admin can delete a page', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $page = Page::factory()->create();
    $this->delete(route("admin.pages.destroy", ['page' => $page, 'locale' => $locale]))->assertRedirect();
});
