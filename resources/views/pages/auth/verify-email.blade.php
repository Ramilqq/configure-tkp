<x-layouts.app>
    <h1>Подтвердите адрес электронной почты</h1>
    <p>Мы отправили письмо с ссылкой для подтверждения на ваш адрес. Не получили?</p>
    <form method="POST" action="{{ route('verification.send') }}">
        @csrf
        <button class="btn btn-primary">Отправить письмо ещё раз</button>
    </form>
    @if (session('status') === 'verification-link-sent')
        <p class="text-success mt-3">Ссылка отправлена!</p>
    @endif
</x-layouts.app>
