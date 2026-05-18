<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'RU-Drive ТКП' }}</title>
        <link rel="icon" href="storage/icon.png" sizes="any">
        <base href="/" />
        {{-- Bootstrap Icons CDN (резерв) --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-body-secondary">

        {{-- ===== NAVBAR ===== --}}
        <nav class="navbar navbar-expand-lg navbar-brand-teal shadow-sm">
            <div class="container-fluid px-3">

                {{-- Логотип --}}
                <a class="navbar-brand d-flex align-items-center gap-2" href="/">
                    <!--svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="28" height="28" rx="5" fill="white" fill-opacity="0.2"/>
                        <path d="M6 14 C6 9.6 9.6 6 14 6 C18.4 6 22 9.6 22 14" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                        <path d="M14 6 L14 14 L20 14" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg-->
                    <!--img src="storage/ru-drive-logo.svg" alt="RU-DRIVE logo"-->
                    <span>RU-DRIVE</span>
                </a>

                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain"
                    aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarMain">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-1">

                        <li class="nav-item">
                            <a class="nav-link rounded px-2 @if(url()->current() == route('home')) active @endif" href="{{ route('home') }}">
                                <i class="bi bi-house-door me-1"></i>Главная
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link rounded px-2 @if(url()->current() == route('table-settings.template-list')) active @endif" href="{{ route('table-settings.template-list') }}">
                                <i class="bi bi-layout-text-sidebar me-1"></i>Шаблон
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link rounded px-2 @if(url()->current() == route('table-settings.products.excel-import')) active @endif" href="{{ route('table-settings.products.excel-import') }}">
                                <i class="bi bi-file-earmark-arrow-up me-1"></i>Импорт/Экспорт
                            </a>
                        </li>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle rounded px-2" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-gear me-1"></i>Настройки
                            </a>
                            <ul class="dropdown-menu">

                                @can('view', new \App\Models\Tkp\Engineering)
                                    <li><a class="dropdown-item @if(url()->current() == route('table-settings.engineering-list')) active @endif"
                                        href="{{ route('table-settings.engineering-list') }}">
                                        <i class="bi bi-cpu me-2"></i>Инженерные данные</a></li>
                                @endcan

                                @can('view', new \App\Models\Tkp\Manufacturer)
                                    <li><a class="dropdown-item @if(url()->current() == route('table-settings.manufacturer-list')) active @endif"
                                        href="{{ route('table-settings.manufacturer-list') }}">
                                        <i class="bi bi-building me-2"></i>Производители</a></li>
                                @endcan

                                @can('view', new \App\Models\Tkp\Delivery)
                                    <li><a class="dropdown-item @if(url()->current() == route('table-settings.delivery-list')) active @endif"
                                        href="{{ route('table-settings.delivery-list') }}">
                                        <i class="bi bi-truck me-2"></i>Доставка</a></li>
                                @endcan

                                @can('view', new \App\Models\Tkp\Industry)
                                    <li><a class="dropdown-item @if(url()->current() == route('table-settings.industry-list')) active @endif"
                                        href="{{ route('table-settings.industry-list') }}">
                                        <i class="bi bi-briefcase me-2"></i>Отрасли</a></li>
                                @endcan

                                @can('view', new \App\Models\Tkp\PaymentScheme)
                                    <li><a class="dropdown-item @if(url()->current() == route('table-settings.payment-schemes')) active @endif"
                                        href="{{ route('table-settings.payment-schemes') }}">
                                        <i class="bi bi-credit-card me-2"></i>Платежные схемы</a></li>
                                @endcan

                                @can('view', new \App\Models\Tkp\ContractOwner)
                                    <li><a class="dropdown-item @if(url()->current() == route('table-settings.contact-owner')) active @endif"
                                        href="{{ route('table-settings.contact-owner') }}">
                                        <i class="bi bi-person-badge me-2"></i>Владелец договора</a></li>
                                @endcan

                                @can('view', new \App\Models\Tkp\ContractOwner)
                                    <li><a class="dropdown-item @if(url()->current() == route('table-settings.user-list')) active @endif"
                                        href="{{ route('table-settings.user-list') }}">
                                        <i class="bi bi-people me-2"></i>Пользователи</a></li>
                                @endcan

                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item @if(url()->current() == route('configuration-node-group')) active @endif"
                                    href="{{ route('configuration-node-group') }}">
                                    <i class="bi bi-sliders me-2"></i>Конфигуратор</a></li>
                            </ul>
                        </li>

                    </ul>

                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('tkp.contact') }}" class="btn-nav-create">
                            <i class="bi bi-plus-lg me-1"></i>Создать ТКП
                        </a>
                        @auth
                        <a href="{{ route('logout') }}" class="btn-nav-logout text-decoration-none">
                            <i class="bi bi-box-arrow-right me-1"></i>{{ auth()->user()->name }}
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        {{-- ===== LOADING BAR (top) ===== --}}
        <div class="rd-loading-bar" wire:loading.delay>
            <div class="rd-loading-bar-inner"></div>
        </div>

        {{-- ===== КОНТЕНТ ===== --}}
        <div class="container-fluid px-3 py-3">
            <div class="bg-white rounded-2 shadow-sm p-3 p-md-4 position-relative">
                {{-- Оверлей поверх контента при загрузке Livewire --}}
                <div class="rd-content-overlay" wire:loading.delay></div>
                {{ $slot }}
            </div>
        </div>

    </body>
</html>
