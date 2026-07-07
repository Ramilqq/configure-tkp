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
                            Строка 3 — заголовки. Строка 4 — tech-ключи <code>[Key]</code>. Данные с 5 строки.
                            Upsert по <b>hash</b> (ЧРП / УПП) или по <b>id</b> (Generic).
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
                        @if(empty($previewRows) || empty($plan) || !empty($plan['blocking_duplicates'] ?? [])) disabled @endif>
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

                        @php($blockingDups = $plan['blocking_duplicates'] ?? [])
                        @if(!empty($blockingDups))
                        <div class="alert alert-danger py-2 small">
                            <b><i class="bi bi-exclamation-octagon me-1"></i>Дубли внутри файла — импорт заблокирован:</b>
                            <ul class="mb-0 mt-1">
                                @foreach($blockingDups as $key => $rowNumbers)
                                <li>{{ is_string($key) && strlen($key) > 16 ? 'hash '.substr($key, 0, 8).'…' : 'id='.$key }} — строки {{ implode(', ', $rowNumbers) }}</li>
                                @endforeach
                            </ul>
                            <div class="mt-1">Несколько строк указывают на один и тот же товар. Исправьте файл и сделайте предпросмотр заново.</div>
                        </div>
                        @endif

                        @php($fullScan = $plan['full_scan'] ?? [])
                        @if(!empty($fullScan))
                        <div class="p-3 bg-light rounded mb-3 small">
                            <div class="fw-semibold mb-1">По всему файлу ({{ $fullScan['scanned_rows'] ?? 0 }} строк)</div>
                            <div>Создать: <b>{{ $fullScan['to_create'] ?? 0 }}</b>@if(isset($fullScan['to_create_auto'])) (+{{ $fullScan['to_create_auto'] }} без id, auto)@endif</div>
                            <div>Обновить: <b>{{ $fullScan['to_update'] ?? 0 }}</b></div>
                            @if(!empty($fullScan['to_wrong_template']))
                            <div class="text-danger">Пропустят (другой шаблон): <b>{{ $fullScan['to_wrong_template'] }}</b></div>
                            @endif
                            @if(!empty($fullScan['to_skip_no_name']))
                            <div class="text-muted">Пропустят (нет наименования, не товар): <b>{{ $fullScan['to_skip_no_name'] }}</b></div>
                            @endif
                        </div>
                        @endif

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

                        @php($garbage = $plan['garbage'] ?? [])
                        @if(!empty($garbage))
                        <div class="p-3 bg-light rounded mt-3 small">
                            <div class="fw-semibold mb-1"><i class="bi bi-recycle me-1"></i>Диагностика (не удаляется автоматически)</div>

                            @php($orphanedOptions = $garbage['orphaned_options'] ?? [])
                            @if(!empty($orphanedOptions))
                            <div class="mt-2">
                                <b>Опции в БД без колонки в файле:</b>
                                <div class="text-muted">
                                    @foreach($orphanedOptions as $o)
                                        <div>{{ $o['name'] ?? '' }} ({{ $o['key'] ?? '' }}) — заполнено значений: {{ $o['non_empty_values'] ?? 0 }}</div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @php($notInFileCount = $garbage['products_not_in_file_count'] ?? 0)
                            @if($notInFileCount > 0)
                            <div class="mt-2">
                                <b>Товары шаблона, не затронутые этим файлом:</b> {{ $notInFileCount }}
                                <details class="mt-1">
                                    <summary class="text-muted">Показать примеры (до 20)</summary>
                                    <div class="text-muted">
                                        @foreach($garbage['products_not_in_file_sample'] ?? [] as $p)
                                            <div>#{{ $p['id'] ?? '' }} — {{ $p['name'] ?? '' }}</div>
                                        @endforeach
                                    </div>
                                </details>
                            </div>
                            @endif

                            @php($dupHashDb = $garbage['duplicate_hash_groups_in_db'] ?? [])
                            @if(!empty($dupHashDb))
                            <div class="mt-2 text-danger">
                                <b>Уже существующие в БД дубли (одинаковый hash):</b>
                                <div>
                                    @foreach($dupHashDb as $hash => $cnt)
                                        <div>hash {{ substr((string)$hash, 0, 8) }}… — {{ $cnt }} товара(ов)</div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if(empty($orphanedOptions) && $notInFileCount === 0 && empty($dupHashDb))
                            <div class="text-muted mt-2">Проблем не найдено.</div>
                            @endif
                        </div>
                        @endif

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
                        <div><b>Режим:</b> {{ $importResult['mode'] ?? 'generic' }} / upsert по {{ $importResult['upsert_key'] ?? 'id' }}</div>
                        <hr class="my-2">
                        <div><b>Просканировано строк:</b> {{ $importResult['scanned_rows'] ?? 0 }}</div>
                        <div class="mt-1"><b>Создано товаров:</b> {{ $importResult['created_products'] ?? 0 }}</div>
                        @php($createdSample = $importResult['created_product_ids_sample'] ?? [])
                        @if(!empty($createdSample))
                        <div class="ms-3 text-muted">Пример ID: {{ implode(', ', $createdSample) }}</div>
                        @endif
                        <div class="mt-1"><b>Обновлено товаров:</b> {{ $importResult['updated_products'] ?? 0 }}</div>
                        <div class="mt-1"><b>Обновлено значений опций (ячейки):</b> {{ $importResult['updated_option_cells'] ?? 0 }}</div>
                        @if(!empty($importResult['skipped_rows_no_name']))
                        <div class="mt-1 text-muted">Пропущено строк без наименования (не товар): {{ $importResult['skipped_rows_no_name'] }}</div>
                        @endif
                        @if(!empty($importResult['skipped_rows_wrong_template']))
                        <div class="mt-1 text-danger">Пропущено строк (другой шаблон): {{ $importResult['skipped_rows_wrong_template'] }}</div>
                        @endif
                        @if(!empty($importResult['skipped_rows_no_name_on_create']))
                        <div class="mt-1 text-muted">Пропущено строк без наименования при создании: {{ $importResult['skipped_rows_no_name_on_create'] }}</div>
                        @endif
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

    {{-- История импортов --}}
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-dark text-white py-2 px-3">
            <span class="small fw-semibold"><i class="bi bi-clock-history me-1"></i>История импортов (последние 20)</span>
        </div>
        <div class="card-body p-0">
            @if($importLogs->isEmpty())
            <div class="text-center text-muted py-4 small">Импортов ещё не было.</div>
            @else
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle mb-0" style="font-size:12px;">
                    <thead class="table-secondary">
                        <tr>
                            <th class="px-2 py-1">Дата</th>
                            <th class="px-2 py-1">Пользователь</th>
                            <th class="px-2 py-1">Шаблон</th>
                            <th class="px-2 py-1">Файл</th>
                            <th class="px-2 py-1">Статус</th>
                            <th class="px-2 py-1">Итог</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($importLogs as $log)
                        <tr class="@if($log->status === 'error') table-danger @endif">
                            <td class="px-2 py-1">{{ $log->created_at?->format('d.m.Y H:i') }}</td>
                            <td class="px-2 py-1">{{ $log->user?->name ?? '—' }}</td>
                            <td class="px-2 py-1">{{ $log->template?->name ?? $log->template_id }}</td>
                            <td class="px-2 py-1">{{ $log->file_name }}</td>
                            <td class="px-2 py-1">{{ $log->status === 'success' ? 'Успешно' : 'Ошибка' }}</td>
                            <td class="px-2 py-1">
                                @if($log->status === 'error')
                                    <span class="text-danger">{{ $log->error_message }}</span>
                                @else
                                    Создано: {{ $log->result['created_products'] ?? 0 }},
                                    Обновлено: {{ $log->result['updated_products'] ?? 0 }},
                                    Опций: {{ $log->result['updated_option_cells'] ?? 0 }}
                                    <details class="mt-1">
                                        <summary class="text-muted">Подробнее</summary>
                                        <pre class="mb-0" style="white-space:pre-wrap;font-size:11px;">{{ json_encode($log->result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                    </details>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
