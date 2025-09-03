<div class="table-responsive">

    <style>
        .disable-table{
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
    <x-blocks.error-message />

    <table class="table">
        <thead>
        <tr>
            <th style="width:50px;">ID</th>
            <th style="width:100px;">Шаблон</th>

            {{-- Колонка Имя --}}
            <th style="width:200px;position:sticky;left:0;">
                Имя
                <input class="form-control form-control-sm mt-1"
                       type="text"
                       placeholder="Поиск..."
                       wire:model.live.debounce.400ms="filters.name">
            </th>

            {{-- Колонка Описание --}}
            <th>
                Описание
                <input class="form-control form-control-sm mt-1"
                       type="text"
                       placeholder="Поиск..."
                       wire:model.live.debounce.400ms="filters.description">
            </th>

            @php
                $engKeys = collect($products->items())
                    ->first()?->engineering ? array_keys(collect($products->items())->first()->engineering) : [];
            @endphp

            {{-- Динамические колонки ENGINEERING --}}
            @foreach($engKeys as $k)
                <th>
                    {{ $k }}
                    <input class="form-control form-control-sm mt-1"
                           type="text"
                           placeholder="Фильтр…"
                           wire:model.live.debounce.400ms="filters.engineering.{{ $k }}">
                </th>
            @endforeach

            {{-- Цена + промежуток --}}
            <th style="min-width:140px;">
                Цена
                <div class="d-flex gap-1 mt-1">
                    <input class="form-control form-control-sm"
                           type="number" step="0.01" placeholder="от"
                           wire:model.live.debounce.400ms="filters.price_from">
                    <input class="form-control form-control-sm"
                           type="number" step="0.01" placeholder="до"
                           wire:model.live.debounce.400ms="filters.price_to">
                </div>
            </th>

            <th style="min-width:140px;">
                Валюта
                <select class="form-select form-select-sm mt-1"
                        wire:model.live.debounce.200ms="filters.currency">
                    <option value="">Все</option>

                    {{-- только значения из TemplateOption->fields --}}
                    @php
                        $product = new \App\Models\TableSettings\Product;
                    @endphp

                    @foreach(($product->allCurrency() ?? []) as $v)
                        <option value="{{ $v }}">{{ $v }}</option>
                    @endforeach

                    {{-- (опционально) показать пункт "Пусто" для незаполненных опций
                    <option value="__EMPTY__">Пусто</option>
                    --}}
                </select>
            </th>

            {{-- Динамические колонки ОПЦИЙ --}}
            @foreach($table_option_col as $key => $val)
                <th style="min-width:160px;">
                    {{ $val }}
                    <select class="form-select form-select-sm mt-1"
                            wire:model.live.debounce.200ms="filters.options.{{ $key }}">
                        <option value="">Все</option>

                        {{-- только значения из TemplateOption->fields --}}
                        @foreach(($optionChoices[$key] ?? []) as $v)
                            <option value="{{ $v }}">{{ $v }}</option>
                        @endforeach

                        {{-- (опционально) показать пункт "Пусто" для незаполненных опций
                        <option value="__EMPTY__">Пусто</option>
                        --}}
                    </select>
                </th>
            @endforeach

            <th style="width:260px;position:sticky;right:0;">
                Действия
                <div class="d-flex gap-2 mt-1">
                    <button class="btn btn-outline-secondary btn-sm" wire:click="resetFilters">Сброс</button>
                    <select class="form-select form-select-sm" style="width:auto" wire:model="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </th>
        </tr>
        </thead>

        <tbody wire:loading.class="disable-table">
        @forelse($products as $product)
            <tr wire:key="row-{{ $product->id }}" class="table-active" style="vertical-align:middle;">
                <th>{{ $product->id }}</th>
                <td>{{ $product->template->name ?? '' }}</td>

                <td style="position:sticky;left:0;">
                    <input type="text"
                           value="{{ $product->name }}"
                           wire:change="saveProductField({{ $product->id }}, 'name', $event.target.value)">
                </td>

                <td>
                    <input type="text"
                           value="{{ $product->description }}"
                           wire:change="saveProductField({{ $product->id }}, 'description', $event.target.value)">
                </td>

                @foreach(($product->engineering ?? []) as $engKey => $engVal)
                    <td>
                        <input type="text"
                               value="{{ $engVal }}"
                               wire:change="saveEngineering({{ $product->id }}, '{{ $engKey }}', $event.target.value)">
                    </td>
                @endforeach

                <td>
                    <input type="number" step="0.01"
                           value="{{ $product->price }}"
                           wire:change="saveProductField({{ $product->id }}, 'price', $event.target.value)">
                </td>
                
                
                <td>
                    <select class="form-select"
                            wire:change="saveProductField({{ $product->id }}, 'currency', $event.target.value)">
                        <option value="">NULL</option>
                        @foreach(($product->allCurrency() ?? []) as $currency)
                            <option value="{{ $currency }}" @selected($product->currency === $currency)>{{ $currency }}</option>
                        @endforeach
                    </select>
                </td>


                @foreach($product->productOption as $opt)
                    <td>
                        <select class="form-select"
                                
                                wire:change="saveProductOption({{ $opt->id }}, $event.target.value)">
                            <option value="">NULL</option>
                            @foreach(($opt->getName->fields ?? []) as $field)
                                <option value="{{ $field }}" @selected($opt->value === $field)>{{ $field }}</option>
                            @endforeach
                        </select>
                    </td>
                @endforeach

                <td style="position:sticky;right:0;">
                    <button class="btn btn-primary btn-sm"
                            title="Изменить продукт"
                            data-bs-toggle="modal"
                            data-bs-target="#productModalForm"
                            @click="$dispatch('productEditOpenForm', {id: {{ $product->id }} })">
                        <i class="bi bi-pencil-square"></i>
                    </button>

                    <button class="btn btn-danger btn-sm"
                            title="Удалить продукт"
                            wire:click="productDellete({{ $product->id }})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr><td colspan="100">Нет записей</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="mt-3">
        {{ $products->onEachSide(1)->links('components.blocks.pagination') }}
    </div>
</div>
