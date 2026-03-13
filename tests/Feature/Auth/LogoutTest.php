<?php

use App\Models\User;

test('authenticated user can logout', function () {
    /** @var \Tests\TestCase $this */
    $user = createUser();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});