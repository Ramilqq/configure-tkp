<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

test('user can request reset link', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser([
        'email' => 'ivan@ru-drive.com',
    ]);

    $response = $this->from(route('password.request'))
        ->post(route('password.email'), [
            'email' => $user->email,
        ]);

    $response->assertRedirect(route('password.request'));
    $response->assertSessionHas('status');
});

test('user can reset password with valid token', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser([
        'email' => 'ivan@ru-drive.com',
        'password' => Hash::make('old-password'),
    ]);

    $token = Password::broker()->createToken($user);

    $response = $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-password123',
        'password_confirmation' => 'new-password123',
    ]);

    $response->assertRedirect(route('login'));

    expect(Hash::check('new-password123', $user->fresh()->password))->toBeTrue();
});