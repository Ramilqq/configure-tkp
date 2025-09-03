<x-layouts.app>
    <h1>Сброс пароля</h1>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Почта</label>
            <input type="email" class="form-control" name="email" required>
            @error('email')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <button class="btn btn-success">Отправить ссылку</button>
    </form>
    @if (session('status'))
        <p class="text-success mt-3">{{ session('status') }}</p>
    @endif
</x-layouts.app>
