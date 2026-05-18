<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'RU-Drive ТКП' }}</title>
        <link rel="icon" href="storage/icon.png" sizes="any">
        <base href="/" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-body">

        <div class="auth-wrapper">

            {{-- ===== Левая брендовая панель ===== --}}
            <div class="auth-brand-side">
                <div class="auth-brand-content">
                    <div class="auth-brand-logo">
                        <!--svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="48" height="48" rx="10" fill="white" fill-opacity="0.15"/>
                            <path d="M10 24 C10 16.3 16.3 10 24 10 C31.7 10 38 16.3 38 24" stroke="white" stroke-width="3.5" stroke-linecap="round"/>
                            <path d="M24 10 L24 24 L35 24" stroke="white" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg-->
                        <span class="auth-brand-name">RU-DRIVE</span>
                    </div>
                    <p class="auth-brand-tagline">Система управления<br>коммерческими предложениями</p>
                    <div class="auth-brand-divider"></div>
                    <ul class="auth-brand-features">
                        <li><i class="bi bi-check-circle-fill me-2"></i>Расчёт ТКП</li>
                        <li><i class="bi bi-check-circle-fill me-2"></i>Генерация PDF</li>
                        <li><i class="bi bi-check-circle-fill me-2"></i>Конфигуратор схем</li>
                        <li><i class="bi bi-check-circle-fill me-2"></i>Управление продуктами</li>
                    </ul>
                </div>
            </div>

            {{-- ===== Правая панель с формой ===== --}}
            <div class="auth-form-side">
                <div class="auth-form-container">
                    {{ $slot }}
                </div>
            </div>

        </div>

    </body>
</html>
