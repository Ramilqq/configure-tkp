<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;

test('user can register', function () {
    /** @var \Tests\TestCase $this */
    Event::fake([Registered::class]);

    $response = $this->post(route('register.store'), [
        'name' => 'Иван',
        'phone' => '+79990000000',
        'email' => 'ivan@ru-drive.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect(route('verification.notice'));

    $user = User::where('email', 'ivan@ru-drive.com')->first();

    expect($user)->not->toBeNull();
    expect($user->role)->toBe(User::USER);
    expect(Hash::check('password123', $user->password))->toBeTrue();

    Event::assertDispatched(Registered::class);
});