<x-layouts.app>
    <h1>Новый пароль</h1>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
            <label class="form-label">Почта</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Пароль</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Повторите пароль</label>
            <input type="password" class="form-control" name="password_confirmation" required>
        </div>
        <button class="btn btn-success">Сохранить</button>
    </form>
</x-layouts.app>
