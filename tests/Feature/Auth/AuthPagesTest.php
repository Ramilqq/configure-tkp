<?php

dataset('guest-auth-pages', [
    'login' => [fn () => route('login')],
    'register' => [fn () => route('register')],
    'forgot password' => [fn () => route('password.request')],
]);

test('guest can open auth pages', function (Closure $route) {
    /** @var \Tests\TestCase $this */
    $this->get($route())->assertOk();
})->with('guest-auth-pages');