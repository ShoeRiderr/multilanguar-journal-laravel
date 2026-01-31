<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard', ['locale' => app()->getLocale()]));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $admin = User::factory()->create(['role' => \App\UserRole::ADMIN]);
    $this->actingAs($admin);

    $response = $this->get(route('dashboard', ['locale' => app()->getLocale()]));
    $response->assertOk();
});

test('user role is redirected to welcome page', function () {
    $user = User::factory()->create(['role' => \App\UserRole::USER]);
    $this->actingAs($user);

    $response = $this->get(route('dashboard', ['locale' => app()->getLocale()]));
    $response->assertRedirect(route('home', ['locale' => app()->getLocale()]));
});