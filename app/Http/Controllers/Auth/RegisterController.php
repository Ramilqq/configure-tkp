<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\EmailDomain;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        return view('pages.auth.register');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255', 'regex:/^\p{L}+(?:-\p{L}+)?\s\p{L}+(?:-\p{L}+)?\s\p{L}+(?:-\p{L}+)?$/u'],
            'phone'    => ['required', 'string', 'max:50', 'regex:/^\+7\d{10}$/'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email', new EmailDomain],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
        ], [
            'name.regex' => 'Поле ФИО: введите фамилию, имя, отчество через пробел (например, Иванов Иван Иванович).',
            'phone.regex' => 'Введите номер телефона в формате +79991234567.',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'phone'    => $data['phone'] ?? null,
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => User::USER,
        ]);

        // Событие отправит письмо для подтверждения
        event(new Registered($user));

        // сразу логинить без подтверждения почты
        // Auth::login($user);

        return redirect()->route('verification.notice')->with('status', 'verification-link-sent');
    }
}
