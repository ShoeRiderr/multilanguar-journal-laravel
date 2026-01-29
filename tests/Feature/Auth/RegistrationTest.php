<?php

test('registration screen can be rendered', function () {
    $response = $this->get(route('register', ['locale' => 'en']));
    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store', ['locale' => 'en']), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);
    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', ['locale' => 'en'], false));
});