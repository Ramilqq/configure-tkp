<x-layouts.auth>
    <h4 class="auth-form-title">Вход в систему</h4>
    <p class="auth-form-subtitle text-muted small mb-4">Введите корпоративные данные для входа</p>

    <form method="POST" action="{{ route('login.store', [], false) }}">
        @csrf

        @if ($errors->any())
        <div class="alert alert-danger py-2 mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            @foreach ($errors->all() as $error)
                <div class="small">{{ $error }}</div>
            @endforeach
        </div>
        @endif

        @if (session('status'))
        <div class="alert alert-success py-2 mb-3 small">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
        </div>
        @endif

        <div class="mb-3">
            <label for="email" class="form-label small fw-semibold">E-Mail</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                    id="email" name="email" value="{{ old('email') }}"
                    placeholder="mail@ru-drive.com" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label small fw-semibold">Пароль</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control @error('password') is-invalid @enderror"
                    id="password" name="password" placeholder="••••••••" required>
            </div>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-auth">
                <i class="bi bi-box-arrow-in-right me-2"></i>Войти
            </button>
        </div>

        <div class="d-flex align-items-center justify-content-between">
            <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-person-plus me-1"></i>Регистрация
            </a>
            <a href="{{ route('password.request') }}" class="text-muted small text-decoration-none">
                Забыли пароль?
            </a>
        </div>
    </form>
</x-layouts.auth>
