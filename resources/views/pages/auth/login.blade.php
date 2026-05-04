<x-layouts.app>
    <form method="POST" action="{{ route('login.store', [], false) }}">
        @csrf
        <legend>Вход в личный кабинет</legend>

        <div class="mb-3">
            <label for="email" class="form-label">Почта</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Пароль</label>
            <input type="password" class="form-control" id="password" name="password" required>
            @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-success">Войти</button>
        <a class="btn btn-primary" href="{{ route('register') }}">Регистрация</a>
        <a class="btn btn-link" href="{{ route('password.request') }}">Забыли пароль?</a>
    </form>
</x-layouts.app>
