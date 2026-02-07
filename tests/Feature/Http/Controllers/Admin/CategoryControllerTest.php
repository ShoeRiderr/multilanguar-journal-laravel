<?php

use App\Models\User;
use App\Models\Category;
use App\Models\Language;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can create category with parent', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $languages = Language::all();
    $parent = Category::factory()->create();
    $translations = [];
    foreach ($languages as $lang) {
        $translations[$lang->id] = [
            'name' => 'Child ' . $lang->code,
            'slug' => 'child-slug-' . $lang->code,
            'language_id' => $lang->id,
        ];
    }
    $response = $this->post("/$locale/admin/categories", [
        'parent_id' => $parent->id,
        'translations' => $translations,
    ]);
    $response->assertRedirect();
    $category = Category::all()->last();
    $this->assertEquals($parent->id, $category->parent_id);
});

test('deletes category_post records when category is deleted', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $post = \App\Models\Post::factory()->create();
    $category = $post->categories()->first();
    // Attach post to category (handle both possible pivot table names)
    $pivotTable = 'category_post';
    $this->assertDatabaseHas($pivotTable, [
        'category_id' => $category->id,
        'post_id' => $post->id,
    ]);
    $this->delete("/$locale/admin/categories/{$category->id}")->assertRedirect();
    $this->assertDatabaseMissing($pivotTable, [
        'category_id' => $category->id,
        'post_id' => $post->id,
    ]);
});

test('denies access to non-admin users', function () {
    $user = User::factory()->create(['role' => 'user']);
    $category = Category::factory()->create();
    $locale = app()->getLocale();
    $routes = [
        'get' => [
            "/$locale/admin/categories",
            "/$locale/admin/categories/create",
            "/$locale/admin/categories/{$category->id}/edit",
        ],
        'post' => [
            "/$locale/admin/categories"
        ],
        'put' => [
            "/$locale/admin/categories/{$category->id}"
        ],
        'delete' => [
            "/$locale/admin/categories/{$category->id}"
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
    $admin = User::factory()->create(['role' => 'admin']);
    $category = Category::factory()->create();
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $this->get("/$locale/admin/categories")->assertOk();
    $this->get("/$locale/admin/categories/create")->assertOk();
    $this->get("/$locale/admin/categories/{$category->id}/edit")->assertOk();
});

test('validates store request', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    // Missing required fields
    $this->post("/$locale/admin/categories", [])->assertSessionHasErrors([
        'translations',
    ]);

    // Invalid parent_id
    $languages = Language::all();
    $category = Category::factory()->create();
    $translations = [];
    foreach ($languages as $lang) {
        $translations[$lang->id] = [
            'name' => 'Test',
            'slug' => 'test-slug',
            'language_id' => $lang->id,
        ];
    }
    $this->post("/$locale/admin/categories", [
        'parent_id' => 9999,
        'translations' => $translations,
    ])->assertSessionHasErrors(['parent_id']);

    // Valid data: add all translations for all languages
    $translations = [];
    foreach ($languages as $lang) {
        $translations[$lang->id] = [
            'name' => 'Test ' . $lang->code,
            'slug' => 'unique-slug-' . $lang->code,
            'language_id' => $lang->id,
        ];
    }
    $response = $this->post("/$locale/admin/categories", [
        'parent_id' => null,
        'translations' => $translations,
    ]);
    $response->assertRedirect();
    $category = Category::all()->last();
    foreach ($languages as $lang) {
        $this->assertDatabaseHas('category_translations', [
            'category_id' => $category->id,
            'language_id' => $lang->id,
            'name' => 'Test ' . $lang->code,
            'slug' => 'unique-slug-' . $lang->code,
        ]);
    }
});

test('validates update request', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $languages = Language::all();
    $category = Category::factory()->create();

    // Missing required fields
    $this->put("/$locale/admin/categories/{$category->id}", [])->assertSessionHasErrors([
        'translations',
    ]);

    // Invalid parent_id (cannot be self)
    $translations = [];
    foreach ($languages as $lang) {
        $translations[$lang->id] = [
            'name' => 'Test',
            'slug' => 'test-slug',
            'language_id' => $lang->id,
        ];
    }
    $this->put("/$locale/admin/categories/{$category->id}", [
        'parent_id' => $category->id,
        'translations' => $translations,
    ])->assertSessionHasErrors(['parent_id']);

    // Valid data: update all translations for all languages
    $translations = [];
    foreach ($languages as $lang) {
        $translations[$lang->id] = [
            'name' => 'Updated ' . $lang->code,
            'slug' => 'updated-slug-' . $lang->code,
            'language_id' => $lang->id,
        ];
    }
    $response = $this->put("/$locale/admin/categories/{$category->id}", [
        'parent_id' => null,
        'translations' => $translations,
    ]);
    $response->assertRedirect();
    foreach ($languages as $lang) {
        $this->assertDatabaseHas('category_translations', [
            'category_id' => $category->id,
            'language_id' => $lang->id,
            'name' => 'Updated ' . $lang->code,
            'slug' => 'updated-slug-' . $lang->code,
        ]);
    }
});

test('admin can delete a category', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $locale = app()->getLocale();
    $category = Category::factory()->create();
    $this->actingAs($admin)
        ->delete(route('admin.categories.destroy', ['category' => $category, 'locale' => $locale]))->assertRedirect();
});
