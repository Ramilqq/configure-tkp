<?php

use Illuminate\Support\Facades\Hash;

test('verified user can login', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser([
        'email' => 'ivan@ru-drive.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => 'ivan@ru-drive.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($user);
});

test('unverified user is redirected to verification notice after login', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser([
        'email' => 'ivan@ru-drive.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => null,
    ]);

    $response = $this->post(route('login.store'), [
        'email' => 'ivan@ru-drive.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('verification.notice'));
    $this->assertAuthenticatedAs($user);
});

test('user cannot login with invalid password', function () {
    /** @var \Tests\TestCase $this */
    createUser([
        'email' => 'ivan@ru-drive.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);

    $response = $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => 'ivan@ru-drive.com',
            'password' => 'wrong-password',
        ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('user cannot login with non corporate email domain', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->from(route('login'))
        ->post(route('login.store'), [
            'email' => 'ivan@gmail.com',
            'password' => 'password123',
        ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});