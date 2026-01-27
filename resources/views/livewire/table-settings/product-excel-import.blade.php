<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Импорт товаров из Excel (несколько листов)</h2>

        <button wire:click="exportData" class="btn btn-primary hover:bg-black disabled:opacity-50" wire:loading.attr="disabled">
            Экспортировать в Excel
        </button>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="p-4 rounded border space-y-4">
            <div>
                <label class="block text-sm font-medium mb-1">Template (куда обновляем товары по id)</label>
                <select wire:model="templateId" class="w-full rounded border p-2">
                    <option value="">— выберите —</option>
                    @foreach($templates as $t)
                        <option value="{{ $t->id }}">{{ $t->id }} — {{ $t->name }}</option>
                    @endforeach
                </select>
                @error('templateId') <div class="alert alert-warning">{{ $message }}</div> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Excel файл (.xlsx)</label>
                <input type="file" wire:model="file" accept=".xlsx,.xls" class="w-full" />
                @error('file') <div class="alert alert-warning">{{ $message }}</div> @enderror
                <div class="text-xs text-gray-500 mt-2">
                    Строка 1 — имена колонок. Строка 2 — подписи (не важно). Данные с 3 строки.
                    Обновление строго по <b>id</b>.
                </div>
            </div>

            @if(!empty($sheets))
                <div>
                    <label class="block text-sm font-medium mb-1">Лист для импорта</label>
                    <select wire:model="sheet" class="w-full rounded border p-2">
                        @foreach($sheets as $s)
                            <option value="{{ $s }}">{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-3">
                    <div class="flex-1">
                        <label class="block text-sm font-medium mb-1">Предпросмотр строк (N)</label>
                        <input type="number" min="1" max="200" wire:model="previewLimit" class="w-full rounded border p-2" />
                        @error('previewLimit') <div class="alert alert-warning">{{ $message }}</div> @enderror
                    </div>

                    <button
                        wire:click="makePreview"
                        class="btn btn-primary hover:bg-blue-700 disabled:opacity-50"
                        wire:loading.attr="disabled"
                    >
                        Предпросмотр
                    </button>
                </div>

                <button
                    wire:click="import"
                    class="btn btn-primary hover:bg-green-700 disabled:opacity-50"
                    wire:loading.attr="disabled"
                    @if(empty($previewRows) || empty($plan)) disabled @endif
                >
                    Импортировать (обновить по id)
                </button>
            @endif

            <div wire:loading class="text-sm text-gray-600">
                Обработка…
            </div>

            @if($error)
                <div class="p-3 rounded bg-red-50 text-red-700 text-sm">
                    {{ $error }}
                </div>
            @endif
        </div>

        <div class="p-4 rounded border space-y-4">
            <h3 class="font-semibold">Что будет обновлено / что добавится</h3>

            @if(!empty($plan))
                <div class="space-y-3 text-sm">
                    @php($dups = $plan['duplicates_in_excel'] ?? [])
                    @if(!empty($dups))
                        <div class="p-3 rounded bg-yellow-50 text-yellow-900">
                            <b>Дубликаты колонок в Excel:</b> {{ implode(', ', $dups) }}
                            <div class="text-xs mt-1">Лучше убрать дубликаты, чтобы не было сюрпризов.</div>
                        </div>
                    @endif

                    <div class="p-3 rounded bg-gray-50">
                        <div class="font-semibold mb-1">Базовые поля товара</div>
                        <div><b>Есть в Excel:</b> {{ implode(', ', $plan['base_fields_present_in_excel'] ?? []) ?: '—' }}</div>
                        <div>
                            <b>Нет в Excel (не будут обновляться):</b>
                            <span class="text-red-700">{{ implode(', ', $plan['base_fields_missing_in_excel'] ?? []) ?: '—' }}</span>
                        </div>
                        <div class="text-xs text-gray-600 mt-1">
                            Если хотите обновлять поле — добавьте колонку в Excel (строка 1).
                        </div>
                    </div>

                    <div class="p-3 rounded bg-gray-50">
                        <div class="font-semibold mb-1">Опции (любые колонки НЕ из базы)</div>

                        <div class="mt-1">
                            <b>Новые колонки → будут созданы в БД как опции:</b>
                            @php($new = $plan['new_option_columns_will_be_created'] ?? [])
                            @if(empty($new))
                                <span class="text-gray-700">—</span>
                            @else
                                <div class="text-green-800 space-y-1">
                                    @foreach($new as $item)
                                        @if(is_array($item))
                                            <div>+ {{ $item['name'] ?? '' }} <span class="text-xs text-gray-600">({{ $item['key'] ?? '' }})</span></div>
                                        @else
                                            <div>+ {{ $item }}</div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="mt-2">
                            <b>Опции есть в БД, но нет в Excel → не будут обновляться этим импортом:</b>
                            @php($miss = $plan['db_option_columns_missing_in_excel_not_updated'] ?? [])
                            @if(empty($miss))
                                <span class="text-gray-700">—</span>
                            @else
                                <div class="text-red-700">{{ implode(', ', $miss) }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="text-sm text-gray-500">
                    Загрузите файл и сделайте предпросмотр — тут появится план изменений.
                </div>
            @endif

            @if(!empty($importResult))
                <div class="p-3 rounded bg-green-50 text-sm">
                    <div class="font-semibold mb-2">Импорт выполнен</div>
                    <div><b>Лист:</b> {{ $importResult['sheet'] ?? '' }}</div>
                    <div><b>Template:</b> {{ $importResult['template_id'] ?? '' }}</div>
                    <hr class="my-2">

                    <div><b>Создано новых опций:</b> {{ $importResult['created_options'] ?? 0 }}</div>
                    <div><b>Просканировано строк:</b> {{ $importResult['scanned_rows'] ?? 0 }}</div>

                    <div class="mt-2"><b>Создано товаров всего:</b> {{ $importResult['created_products'] ?? 0 }}</div>
                    <div class="ml-3">— с указанным id: {{ $importResult['created_products_with_id'] ?? 0 }}</div>
                    <div class="ml-3">— без id (auto): {{ $importResult['created_products_auto_id'] ?? 0 }}</div>

                    @php($createdSample = $importResult['created_product_ids_sample'] ?? [])
                    @if(!empty($createdSample))
                        <div class="mt-1 text-xs text-gray-700">
                            <b>Пример созданных ID:</b> {{ implode(', ', $createdSample) }}
                        </div>
                    @endif

                    <div class="mt-2"><b>Обновлено товаров:</b> {{ $importResult['updated_products'] ?? 0 }}</div>

                    <div class="mt-2"><b>Пропущено (id есть, но другой template):</b> {{ $importResult['skipped_rows_wrong_template'] ?? 0 }}</div>
                    @php($wrong = $importResult['wrong_template_ids_sample'] ?? [])
                    @if(!empty($wrong))
                        <div class="text-xs text-red-700">
                            <b>Пример ID (другой template):</b> {{ implode(', ', $wrong) }}
                        </div>
                    @endif

                    <div class="mt-2"><b>Пропущено (нет name при создании):</b> {{ $importResult['skipped_rows_no_name_on_create'] ?? 0 }}</div>

                    <div class="mt-2"><b>Обновлено значений опций (ячейки):</b> {{ $importResult['updated_option_cells'] ?? 0 }}</div>
                </div>
            @endif
        </div>
    </div>

    @if(!empty($previewRows) && !empty($previewColumns))
        <div class="p-4 rounded border">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold">Предпросмотр первых {{ $previewLimit }} строк</h3>
                <div class="text-xs text-gray-500">
                    _status: UPDATE=обновим, CREATE=создадим с id, CREATE_AUTO=создадим (auto id), WRONG_TEMPLATE=пропустим
                </div>
            </div>

            <div class="overflow-auto">
                <table class="min-w-full text-sm border">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach($previewColumns as $col)
                                <th class="border px-2 py-1 text-left whitespace-nowrap">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewRows as $r)
                            <tr class="@if(($r['_status'] ?? '') === 'WRONG_TEMPLATE') bg-red-50 @elseif(in_array(($r['_status'] ?? ''), ['CREATE','CREATE_AUTO'], true)) bg-green-50 @endif">
                                @foreach($previewColumns as $col)
                                    <td class="border px-2 py-1 whitespace-nowrap">
                                        {{ is_array($r[$col] ?? null) ? json_encode($r[$col], JSON_UNESCAPED_UNICODE) : ($r[$col] ?? '') }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
