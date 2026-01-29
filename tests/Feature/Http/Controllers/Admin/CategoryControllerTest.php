<?php
use App\Models\User;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\Language;
use App\UserRole;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function Pest\Laravel\delete;

test('denies access to non-admin users', function () {
    actingAs($this->user);
    $routes = [
        'get' => [
            "/{$this->locale}/admin/categories",
            "/{$this->locale}/admin/categories/create",
            "/{$this->locale}/admin/categories/{$this->category->id}/edit",
        ],
        'post' => [
            "/{$this->locale}/admin/categories"
        ],
        'put' => [
            "/{$this->locale}/admin/categories/{$this->category->id}"
        ],
        'delete' => [
            "/{$this->locale}/admin/categories/{$this->category->id}"
        ],
    ];
    foreach ($routes['get'] as $route) {
        get($route)->assertForbidden();
    }
    foreach ($routes['post'] as $route) {
        post($route, [])->assertForbidden();
    }
    foreach ($routes['put'] as $route) {
        put($route, [])->assertForbidden();
    }
    foreach ($routes['delete'] as $route) {
        delete($route)->assertForbidden();
    }
});

test('allows access to admin users', function () {
    actingAs($this->admin);
    get("/{$this->locale}/admin/categories")->assertOk();
    get("/{$this->locale}/admin/categories/create")->assertOk();
    get("/{$this->locale}/admin/categories/{$this->category->id}/edit")->assertOk();
});

test('validates store request', function () {
    actingAs($this->admin);
    // Missing required fields
    post("/{$this->locale}/admin/categories", [])->assertSessionHasErrors([
        'category_id', 'language_id', 'name', 'slug'
    ]);

    // Invalid parent_id
    post("/{$this->locale}/admin/categories", [
        'parent_id' => 9999,
        'category_id' => $this->categoryTranslation->id,
        'language_id' => $this->categoryTranslation->id,
        'name' => 'Test',
        'slug' => 'test-slug',
    ])->assertSessionHasErrors(['parent_id']);

    // Valid data
    $data = [
        'parent_id' => null,
        'category_id' => $this->categoryTranslation->id,
        'language_id' => $this->categoryTranslation->id,
        'name' => 'Test',
        'slug' => 'unique-slug',
    ];
    post("/{$this->locale}/admin/categories", $data)->assertRedirect();
});

test('validates update request', function () {
    actingAs($this->admin);
    // Missing required fields
    put("/{$this->locale}/admin/categories/{$this->category->id}", [])->assertSessionHasErrors([
        'category_id', 'language_id', 'name', 'slug'
    ]);

    // Invalid parent_id (cannot be self)
    put("/{$this->locale}/admin/categories/{$this->category->id}", [
        'parent_id' => $this->category->id,
        'category_id' => $this->categoryTranslation->id,
        'language_id' => $this->categoryTranslation->id,
        'name' => 'Test',
        'slug' => 'test-slug',
    ])->assertSessionHasErrors(['parent_id']);

    // Valid data
    $data = [
        'parent_id' => null,
        'category_id' => $this->categoryTranslation->id,
        'language_id' => $this->categoryTranslation->id,
        'name' => 'Test',
        'slug' => 'unique-update-slug',
    ];
    put("/{$this->locale}/admin/categories/{$this->category->id}", $data)->assertRedirect();
});

test('admin can delete a category', function () {
    actingAs($this->admin);
    $category = Category::factory()->create();
    delete("/{$this->locale}/admin/categories/{$category->id}")->assertRedirect();
});
