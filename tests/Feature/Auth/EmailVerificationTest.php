<?php

use App\Models\User;
use Illuminate\Support\Facades\URL;

test('authenticated user can verify email with valid signed link', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser([
        'email_verified_at' => null,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(30),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    $response->assertRedirect(route('home'));

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('guest cannot verify email', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser([
        'email_verified_at' => null,
    ]);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(30),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]
    );

    $this->get($verificationUrl)
        ->assertRedirect(route('login'));
});

test('verification fails with invalid signature', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser([
        'email_verified_at' => null,
    ]);

    $url = route('verification.verify', [
        'id' => $user->id,
        'hash' => sha1($user->email),
    ]);

    $this->actingAs($user)
        ->get($url)
        ->assertForbidden();
});