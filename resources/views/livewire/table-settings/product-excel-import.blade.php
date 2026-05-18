<div>
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-semibold"><i class="bi bi-file-earmark-arrow-up me-2 text-success"></i>Импорт / Экспорт товаров Excel</h5>
        <button wire:click="exportData" class="btn btn-primary btn-sm" wire:loading.attr="disabled">
            <i class="bi bi-file-earmark-arrow-down me-1"></i>Экспортировать в Excel
        </button>
    </div>

    <div class="row g-3">

        {{-- Панель загрузки --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white py-2 px-3">
                    <span class="small fw-semibold"><i class="bi bi-upload me-1"></i>Импорт</span>
                </div>
                <div class="card-body p-3">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Template (куда обновляем товары по id)</label>
                        <select wire:model="templateId" class="form-select form-select-sm">
                            <option value="">— выберите —</option>
                            @foreach($templates as $t)
                                <option value="{{ $t->id }}">{{ $t->id }} — {{ $t->name }}</option>
                            @endforeach
                        </select>
                        @error('templateId') <div class="text-warning small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Excel файл (.xlsx)</label>
                        <input type="file" wire:model="file" accept=".xlsx,.xls" class="form-control form-control-sm" />
                        @error('file') <div class="text-warning small mt-1">{{ $message }}</div> @enderror
                        <div class="text-muted small mt-1">
                            Строка 1 — имена колонок. Строка 2 — подписи (не важно). Данные с 3 строки.
                            Обновление строго по <b>id</b>.
                        </div>
                    </div>

                    @if(!empty($sheets))

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Лист для импорта</label>
                        <select wire:model="sheet" class="form-select form-select-sm">
                            @foreach($sheets as $s)
                                <option value="{{ $s }}">{{ $s }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 align-items-end mb-3">
                        <div class="col">
                            <label class="form-label small fw-semibold">Предпросмотр строк (N)</label>
                            <input type="number" min="1" max="200" wire:model="previewLimit" class="form-control form-control-sm" />
                            @error('previewLimit') <div class="text-warning small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-auto">
                            <button wire:click="makePreview" class="btn btn-outline-primary btn-sm" wire:loading.attr="disabled">
                                <i class="bi bi-eye me-1"></i>Предпросмотр
                            </button>
                        </div>
                    </div>

                    <button wire:click="import" class="btn btn-success btn-sm w-100" wire:loading.attr="disabled"
                        @if(empty($previewRows) || empty($plan)) disabled @endif>
                        <i class="bi bi-cloud-upload me-1"></i>Импортировать (обновить по id)
                    </button>

                    @endif

                    <div wire:loading class="text-muted small mt-2">
                        <i class="bi bi-hourglass-split me-1"></i>Обработка…
                    </div>

                    @if($error)
                    <div class="alert alert-danger alert-sm mt-3 mb-0 py-2 small">{{ $error }}</div>
                    @endif

                </div>
            </div>
        </div>

        {{-- Панель плана изменений --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white py-2 px-3">
                    <span class="small fw-semibold"><i class="bi bi-list-check me-1"></i>Что будет обновлено</span>
                </div>
                <div class="card-body p-3">

                    @if(!empty($plan))

                        @php($dups = $plan['duplicates_in_excel'] ?? [])
                        @if(!empty($dups))
                        <div class="alert alert-warning py-2 small">
                            <b>Дубликаты колонок в Excel:</b> {{ implode(', ', $dups) }}
                            <div class="mt-1">Лучше убрать дубликаты, чтобы не было сюрпризов.</div>
                        </div>
                        @endif

                        <div class="p-3 bg-light rounded mb-3 small">
                            <div class="fw-semibold mb-1">Базовые поля товара</div>
                            <div><b>Есть в Excel:</b> {{ implode(', ', $plan['base_fields_present_in_excel'] ?? []) ?: '—' }}</div>
                            <div>
                                <b>Нет в Excel (не будут обновляться):</b>
                                <span class="text-danger">{{ implode(', ', $plan['base_fields_missing_in_excel'] ?? []) ?: '—' }}</span>
                            </div>
                            <div class="text-muted mt-1">Если хотите обновлять поле — добавьте колонку в Excel (строка 1).</div>
                        </div>

                        <div class="p-3 bg-light rounded small">
                            <div class="fw-semibold mb-1">Опции (любые колонки НЕ из базы)</div>
                            <div class="mt-2">
                                <b>Новые колонки → будут созданы в БД как опции:</b>
                                @php($new = $plan['new_option_columns_will_be_created'] ?? [])
                                @if(empty($new))
                                    <span class="text-muted">—</span>
                                @else
                                    <div class="text-success">
                                        @foreach($new as $item)
                                            @if(is_array($item))
                                                <div>+ {{ $item['name'] ?? '' }} <span class="text-muted">({{ $item['key'] ?? '' }})</span></div>
                                            @else
                                                <div>+ {{ $item }}</div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="mt-2">
                                <b>Опции есть в БД, но нет в Excel → не будут обновляться:</b>
                                @php($miss = $plan['db_option_columns_missing_in_excel_not_updated'] ?? [])
                                @if(empty($miss))
                                    <span class="text-muted">—</span>
                                @else
                                    <div class="text-danger">{{ implode(', ', $miss) }}</div>
                                @endif
                            </div>
                        </div>

                    @else
                    <div class="text-center text-muted py-4 small">
                        <i class="bi bi-arrow-left-circle fs-3 d-block mb-2"></i>
                        Загрузите файл и сделайте предпросмотр — тут появится план изменений.
                    </div>
                    @endif

                    @if(!empty($importResult))
                    <div class="alert alert-success mt-3 py-2 small mb-0">
                        <div class="fw-semibold mb-2"><i class="bi bi-check-circle me-1"></i>Импорт выполнен</div>
                        <div><b>Лист:</b> {{ $importResult['sheet'] ?? '' }}</div>
                        <div><b>Template:</b> {{ $importResult['template_id'] ?? '' }}</div>
                        <hr class="my-2">
                        <div><b>Создано новых опций:</b> {{ $importResult['created_options'] ?? 0 }}</div>
                        <div><b>Просканировано строк:</b> {{ $importResult['scanned_rows'] ?? 0 }}</div>
                        <div class="mt-1"><b>Создано товаров всего:</b> {{ $importResult['created_products'] ?? 0 }}</div>
                        <div class="ms-3">— с указанным id: {{ $importResult['created_products_with_id'] ?? 0 }}</div>
                        <div class="ms-3">— без id (auto): {{ $importResult['created_products_auto_id'] ?? 0 }}</div>
                        @php($createdSample = $importResult['created_product_ids_sample'] ?? [])
                        @if(!empty($createdSample))
                        <div class="mt-1 text-muted"><b>Пример созданных ID:</b> {{ implode(', ', $createdSample) }}</div>
                        @endif
                        <div class="mt-1"><b>Обновлено товаров:</b> {{ $importResult['updated_products'] ?? 0 }}</div>
                        <div class="mt-1"><b>Пропущено (другой template):</b> {{ $importResult['skipped_rows_wrong_template'] ?? 0 }}</div>
                        @php($wrong = $importResult['wrong_template_ids_sample'] ?? [])
                        @if(!empty($wrong))
                        <div class="text-danger"><b>Пример ID (другой template):</b> {{ implode(', ', $wrong) }}</div>
                        @endif
                        <div class="mt-1"><b>Пропущено (нет name при создании):</b> {{ $importResult['skipped_rows_no_name_on_create'] ?? 0 }}</div>
                        <div class="mt-1"><b>Обновлено значений опций (ячейки):</b> {{ $importResult['updated_option_cells'] ?? 0 }}</div>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- Предпросмотр таблицы --}}
    @if(!empty($previewRows) && !empty($previewColumns))
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-dark text-white py-2 px-3 d-flex align-items-center justify-content-between">
            <span class="small fw-semibold"><i class="bi bi-table me-1"></i>Предпросмотр первых {{ $previewLimit }} строк</span>
            <span class="text-muted small">_status: UPDATE=обновим, CREATE=создадим с id, CREATE_AUTO=auto id, WRONG_TEMPLATE=пропустим</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0" style="font-size:12px; white-space:nowrap;">
                    <thead class="table-secondary">
                        <tr>
                            @foreach($previewColumns as $col)
                                <th class="px-2 py-1">{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewRows as $r)
                        <tr class="
                            @if(($r['_status'] ?? '') === 'WRONG_TEMPLATE') table-danger
                            @elseif(in_array(($r['_status'] ?? ''), ['CREATE','CREATE_AUTO'], true)) table-success
                            @endif
                        ">
                            @foreach($previewColumns as $col)
                            <td class="px-2 py-1">
                                {{ is_array($r[$col] ?? null) ? json_encode($r[$col], JSON_UNESCAPED_UNICODE) : ($r[$col] ?? '') }}
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
