<html>
    <head>
        <title>{{ $title ?? 'Todo Manager' }}</title>
        <link rel="icon" href="storage/icon.png" sizes="any">

        <base href="/" />
        {{-- Подключение иконок резервный --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body>
        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="/">RU-Drive ТКП</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link @if(url()->current() == route('home')) active @endif" aria-current="page" href="{{route('home')}}">Главная</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if(url()->current() == route('table-settings.template-list')) active @endif" href="{{route('table-settings.template-list')}}">Шаблон</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link @if(url()->current() == route('table-settings.products.excel-import')) active @endif" href="{{route('table-settings.products.excel-import')}}">Импорт/Экспорт</a>
                        </li>

                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Настройки
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item @if(url()->current() == route('tkp.engineering-list')) active @endif" href="{{ route('tkp.engineering-list') }}">Инженерные данные</a></li>
                                <li><a class="dropdown-item @if(url()->current() == route('tkp.manufacturer-list')) active @endif" href="{{ route('tkp.manufacturer-list') }}">Производители</a></li>
                                <li><a class="dropdown-item @if(url()->current() == route('configuration-node-group')) active @endif" href="{{route('configuration-node-group')}}">Конфигуратор настройка</a></li>
                            </ul>
                        </li>

                        <li class="nav-item d-flex">
                            <a class="@if(url()->current() == route('tkp.contact')) active @endif btn btn-success" href="{{route('tkp.contact')}}">Создать ТКП</a>
                        </li>
                        
                    </ul>
                    <!--form class="d-flex" role="search">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form-->
                </div>
            </div>
        </nav>

        <div class="container border  rounded-1 m-auto p-5">
            {{ $slot }}
        </div>

        
    </body>
</html>