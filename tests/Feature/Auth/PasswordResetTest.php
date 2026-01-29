<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get(route('password.request', ['locale' => 'en']));
    $response->assertOk();
});

test('reset password link can be requested', function () {
    Notification::fake();
    $user = User::factory()->create();
    $this->post(route('password.email', ['locale' => 'en']), ['email' => $user->email]);
    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();
    $user = User::factory()->create();
    $this->post(route('password.email', ['locale' => 'en']), ['email' => $user->email]);
    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get(route('password.reset', ['token' => $notification->token, 'locale' => 'en']));
        $response->assertOk();
        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();
    $user = User::factory()->create();
    $this->post(route('password.email', ['locale' => 'en']), ['email' => $user->email]);
    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post(route('password.update', ['locale' => 'en']), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login', ['locale' => 'en']));
        return true;
    });
});

test('password cannot be reset with invalid token', function () {
    $user = User::factory()->create();
    $response = $this->post(route('password.update', ['locale' => 'en']), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);
    $response->assertSessionHasErrors('email');
});