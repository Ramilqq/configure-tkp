<x-layouts.app>
    <form method="POST" action="{{ route('register.store', [], false) }}">
        @csrf
        <legend>Регистрация учетной записи</legend>

        <div class="mb-3">
            <label for="name" class="form-label">ФИО *</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Фамилия Имя Отчество" required>
            @error('name')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Телефон *</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+79991234567" required>
            @error('phone')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-Mail *</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
            <small id="emailHelp" class="form-text text-muted">Разрешена только корпоративная почта</small>
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Пароль *</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <small id="passwordHelp" class="form-text text-muted">Минимум 8 символов, максимум 20</small>
            @error('password')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Повторите пароль *</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>

        <button type="submit" class="btn btn-success">Регистрация</button>
        <a class="btn btn-primary" href="{{ route('login') }}">Войти</a>
    </form>
</x-layouts.app>
