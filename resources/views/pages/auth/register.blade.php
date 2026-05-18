<x-layouts.auth>
    <h4 class="auth-form-title">Регистрация</h4>
    <p class="auth-form-subtitle text-muted small mb-4">Создайте корпоративную учётную запись</p>

    <form method="POST" action="{{ route('register.store', [], false) }}">
        @csrf

        @if ($errors->any())
        <div class="alert alert-danger py-2 mb-3">
            <i class="bi bi-exclamation-triangle me-2"></i>
            @foreach ($errors->all() as $error)
                <div class="small">{{ $error }}</div>
            @endforeach
        </div>
        @endif

        <div class="mb-3">
            <label for="name" class="form-label small fw-semibold">ФИО <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-person"></i></span>
                <input type="text" class="form-control @error('name') is-invalid @enderror"
                    id="name" name="name" value="{{ old('name') }}"
                    placeholder="Фамилия Имя Отчество" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label small fw-semibold">Телефон <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                <input type="text" class="form-control @error('phone') is-invalid @enderror"
                    id="phone" name="phone" value="{{ old('phone') }}"
                    placeholder="+79991234567" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label small fw-semibold">E-Mail <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                    id="email" name="email" value="{{ old('email') }}"
                    placeholder="mail@ru-drive.com" required>
            </div>
            <div class="form-text text-muted small">
                <i class="bi bi-info-circle me-1"></i>Разрешена только корпоративная почта
            </div>
        </div>

        <div class="row g-2 mb-4">
            <div class="col-12 col-sm-6">
                <label for="password" class="form-label small fw-semibold">Пароль <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" name="password" placeholder="••••••••" required>
                </div>
                <div class="form-text text-muted small">От 8 до 20 символов</div>
            </div>
            <div class="col-12 col-sm-6">
                <label for="password_confirmation" class="form-label small fw-semibold">Повторите <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control"
                        id="password_confirmation" name="password_confirmation"
                        placeholder="••••••••" required>
                </div>
            </div>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-primary btn-auth">
                <i class="bi bi-person-check me-2"></i>Зарегистрироваться
            </button>
        </div>

        <div class="text-center">
            <span class="text-muted small">Уже есть аккаунт?</span>
            <a href="{{ route('login') }}" class="btn btn-link btn-sm p-0 ms-1 text-decoration-none">
                Войти
            </a>
        </div>
    </form>
</x-layouts.auth>
