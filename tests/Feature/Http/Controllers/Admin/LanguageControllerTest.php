<?php

use App\Models\User;
use App\Models\Language;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can create language', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $data = [
        'name' => 'Test Language',
        'code' => 'tl',
        'native_name' => 'Test Native Name',
    ];
    $response = $this->post(route('admin.languages.store', ['locale' => $locale]), $data);
    $response->assertRedirect();
    $this->assertDatabaseHas('languages', $data);
});

test('denies access to non-admin users', function () {
    $user = User::factory()->create(['role' => UserRole::USER]);
    $language = Language::first();
    $locale = app()->getLocale();
    $routes = [
        'get' => [
            route('admin.languages.index', ['locale' => $locale]),
            route('admin.languages.create', ['locale' => $locale]),
            route('admin.languages.edit', ['language' => $language, 'locale' => $locale]),
        ],
        'post' => [
            route('admin.languages.store', ['locale' => $locale]),
        ],
        'put' => [
            route('admin.languages.update', ['language' => $language, 'locale' => $locale]),
        ],
        'delete' => [
            route('admin.languages.destroy', ['language' => $language, 'locale' => $locale]),
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
    $language = Language::first();
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $this->get(route('admin.languages.index', ['locale' => $locale]))->assertOk();
    $this->get(route('admin.languages.create', ['locale' => $locale]))->assertOk();
    $this->get(route('admin.languages.edit', ['language' => $language, 'locale' => $locale]))->assertOk();
});

test('admin can update language', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $language = Language::first();
    $data = [
        'name' => 'Updated Language',
        'code' => 'ul',
        'native_name' => 'Updated Native Name',
    ];
    $response = $this->put(route('admin.languages.update', ['language' => $language, 'locale' => $locale]), $data);
    $response->assertRedirect();
    $this->assertDatabaseHas('languages', $data);
});

test('admin can delete a language', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);
    $locale = app()->getLocale();
    $this->actingAs($admin);
    $language = Language::first();
    $this->delete(route('admin.languages.destroy', ['language' => $language, 'locale' => $locale]))->assertRedirect();
});
