<div>
    @if(!isset($saved_schema['nodes']))

        <div class="text-center py-5">
            <i class="bi bi-exclamation-triangle fs-1 text-warning d-block mb-3"></i>
            <p class="text-muted mb-3">Нет данных для создания ТКП</p>
            <a href="{{ route('tkp.contact.edit', ['id' => $id, 'tkp_version' => $tkp_version]) }}" class="btn btn-primary">
                <i class="bi bi-arrow-right me-1"></i>Продолжить
            </a>
        </div>

    @else

    {{-- Панель навигации по страницам проекта --}}
    <div class="page-nav-bar mb-3">
        <span class="text-muted small fw-semibold me-2">Страницы проекта:</span>
        <a href="{{ route('tkp.contact.edit', ['id' => $id, 'tkp_version' => $tkp_version]) }}" class="btn btn-outline-secondary btn-sm" target="_blank" title="Контактная информация">
            <i class="bi bi-person-lines-fill me-1"></i><span class="d-none d-md-inline">Контакты</span>
        </a>
        <a href="{{ route('tkp.sheme.edit', ['id' => $id, 'tkp_version' => $tkp_version]) }}" class="btn btn-outline-secondary btn-sm" target="_blank" title="Схема">
            <i class="bi bi-diagram-3-fill me-1"></i><span class="d-none d-md-inline">Схема</span>
        </a>
        <a href="{{ route('tkp.delivery.edit', ['id' => $id, 'tkp_version' => $tkp_version]) }}" class="btn btn-outline-secondary btn-sm" target="_blank" title="Доставка">
            <i class="bi bi-truck-front-fill me-1"></i><span class="d-none d-md-inline">Доставка</span>
        </a>
    </div>

    <div class="row g-3">

        {{-- ======= ЛЕВЫЙ САЙДБАР ======= --}}
        <div class="col-12 col-lg-3 calc-sidebar">

            {{-- Дополнительные параметры --}}
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-2 px-3">
                    <span class="small fw-semibold"><i class="bi bi-sliders me-1"></i>Доп. параметры</span>
                </div>
                <div class="card-body p-3">

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="small text-muted">Обновить курс валют:</span>
                        <button title="Обновить" class="btn btn-outline-primary btn-sm" wire:click="currency()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small mb-1">Расходы на продвижение (%)</label>
                        <input type="text" class="form-control form-control-sm" wire:model.lazy="form.pay_params.marketing" wire:loading.attr="disabled">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small mb-1">НДС</label>
                        <select class="form-select form-select-sm" wire:model.lazy="form.pay_params.nds" wire:loading.attr="disabled">
                            <option value="0">0%</option>
                            <option value="18">18%</option>
                            <option value="20">20%</option>
                            <option value="22">22%</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small mb-1">Резерв на изменение (%)</label>
                        <input type="text" class="form-control form-control-sm" wire:model.lazy="form.pay_params.reserve" wire:loading.attr="disabled">
                    </div>

                </div>
            </div>

            {{-- Версия и сохранение --}}
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-2 px-3">
                    <span class="small fw-semibold"><i class="bi bi-floppy me-1"></i>Версия / Сохранение</span>
                </div>
                <div class="card-body p-3">

                    <div class="mb-3">
                        <label class="form-label small mb-1">Комментарий к версии</label>
                        <input type="text" class="form-control form-control-sm" wire:model.lazy="form.comments">
                    </div>

                    <div class="d-grid gap-2">
                        <button wire:click="saveParams()" wire:loading.attr="disabled" type="button" class="btn btn-primary btn-sm">
                            <i class="bi bi-floppy me-1"></i>Сохранить изменения
                        </button>
                        <a target="_blank" href="{{ route('tkp.pdf.show', ['id' => $id, 'tkp_version' => $tkp_version]) }}" class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Открыть PDF
                        </a>
                    </div>

                </div>
            </div>

            {{-- Создать копию --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-2 px-3">
                    <span class="small fw-semibold"><i class="bi bi-copy me-1"></i>Создать копию</span>
                </div>
                <div class="card-body p-3">
                    <div class="mb-2">
                        <input type="text" class="form-control form-control-sm" wire:model="dublicate_comments" placeholder="Комментарий к новой версии">
                        <div class="text-danger small mt-1">@error('dublicate_comments') {{ $message }} @enderror</div>
                    </div>
                    <button wire:click="saveDublicate()" title="Создать копию" wire:loading.attr="disabled" type="button" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-copy me-1"></i>Создать копию
                    </button>
                </div>
            </div>

        </div>

        {{-- ======= ПРАВАЯ ЧАСТЬ ======= --}}
        <div class="col-12 col-lg-9">

            {{-- Список продуктов --}}
            <div class="card mb-3 border-0 shadow-sm">
                <div class="card-header bg-dark text-white py-2 px-3 d-flex align-items-center justify-content-between">
                    <span class="small fw-semibold"><i class="bi bi-box-seam me-1"></i>Список продуктов</span>
                    <livewire:tkp.modal.add-product :tkp_version="$tkp_version" :banks="$banks"/>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered product-table align-middle mb-0">
                            <thead class="table-secondary">
                                <tr>
                                    <th class="text-center" style="width:50px;">#</th>
                                    <th>Наименование</th>
                                    <th>Цена</th>
                                    <th>Валюта</th>
                                    <th>Курс</th>
                                    <th>Доставка</th>
                                    <th class="text-center" style="width:90px;">Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($saved_schema['nodes'] as $nodes)
                                    @php if (!isset($nodes['product'])) continue; @endphp
                                    <tr>
                                        <td class="text-center text-muted">{{ $nodes['product']['id'] }}</td>
                                        <td>{{ $nodes['product']['name'] }}</td>
                                        <td>{{ $nodes['product']['price'] }}</td>
                                        <td><span class="badge bg-secondary">{{ $nodes['product']['currency'] }}</span></td>
                                        <td>{{ $nodes['product']['currency_val'] }}</td>
                                        <td>{{ $nodes['product']['delivery'] }} <small class="text-muted">RUB</small></td>
                                        <td class="text-center">
                                            <button title="Показатели надёжности" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editIndicatorsReliability"
                                                @click="$dispatch('editIndicatorsReliabilityOpenForm', {tkp_version : {{$tkp_version}}, hash : '{{$nodes['product']['hash']}}' })">
                                                <i class="bi bi-shield-check"></i>
                                            </button>
                                            <button title="Изменить продукт" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductForm"
                                                @click="$dispatch('addProductOpenForm', {product_id : '{{$nodes['id']}}' })">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse

                                @forelse($saved_schema['connections'] as $nodes)
                                    @if(isset($nodes['params']['product']))
                                        <tr>
                                            <td class="text-center text-muted">{{ $nodes['params']['product']['id'] }}</td>
                                            <td>{{ $nodes['params']['product']['name'] }}</td>
                                            <td>{{ $nodes['params']['product']['price'] }}</td>
                                            <td><span class="badge bg-secondary">{{ $nodes['params']['product']['currency'] }}</span></td>
                                            <td>{{ $nodes['params']['product']['currency_val'] }}</td>
                                            <td>{{ $nodes['params']['product']['delivery'] }} <small class="text-muted">RUB</small></td>
                                            <td class="text-center text-muted small">
                                                <button title="Изменить продукт" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductForm"
                                                    @click="$dispatch('addProductOpenForm', {product_id : '{{$nodes['params']['id']}}' })">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                @empty
                                @endforelse

                                @if(isset($saved_schema['other']))
                                    @forelse($saved_schema['other'] as $nodes)
                                        <tr>
                                            <td class="text-center text-muted">{{ $nodes['product']['id'] }}</td>
                                            <td>{{ $nodes['product']['name'] }}</td>
                                            <td>{{ $nodes['product']['price'] }}</td>
                                            <td><span class="badge bg-secondary">{{ $nodes['product']['currency'] }}</span></td>
                                            <td>{{ $nodes['product']['currency_val'] }}</td>
                                            <td>{{ $nodes['product']['delivery'] }} <small class="text-muted">RUB</small></td>
                                            <td class="text-center">
                                                <button title="Изменить продукт" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductForm"
                                                    @click="$dispatch('addProductOpenForm', {product_id : '{{$nodes['id']}}' })">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button title="Удалить продукт" class="btn btn-outline-danger btn-sm"
                                                    @click="$dispatch('addProductRemove', {product_id : '{{$nodes['id']}}' })">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <livewire:tkp.modal.indicators-reliability/>

    {{-- ======= БОЛЬШАЯ ТАБЛИЦА РАСЧЁТА ======= --}}
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header bg-dark text-white py-2 px-3">
            <span class="small fw-semibold"><i class="bi bi-table me-1"></i>Таблица расчёта ТКП</span>
        </div>
        <div class="card-body p-0">
            <div class="calc-table-wrap">

@php
    // Колонки + структура таблицы
    $table = [
        'col' => [
            '0' =>  '0 N пп',
            '1' =>  '1 Завод-изготовитель',
            '2' =>  '2 Тип, марка изделия',
            '3' =>  '3 Наименование',
            '4' =>  '4 Кол-во, шт.',
            '5' =>  '5 Стоимость единицы, руб. без НДС',
            '6' =>  '6 Стоимость , руб. без НДС',
            '7' =>  '7 Скидка, %.',
            '8' =>  '8 Общая сумма со скидкой, руб. без НДС',
            '9' =>  '9 Примечание',
            '10' => '10 Коэффициент продажной цены',
            '11' => '11 Бюджет реализации в объемах, руб.',
            '12' => '12 Услуги ген. Подряда, руб.',
            '13' => '13 Расходы на кредитование, руб.',
            '14' => '14 Расходы на ТКП, руб.',
            '15' => '15 Расходы на продвижение, руб.',
            '16' => '16 Запас на риски (Курсовая разница и пр.), руб.',
            '17' => '17 ИТОГО расходы, руб. без НДС',
            '18' => '18 Плановая себестоимость оборудования и материалов, руб. без НДС',
            '19' => '19 Резерв на изменения, руб.',
            '20' => '20 ТЗР закупки, руб.',
            '21' => '21 Итого по закупке, руб. без НДС',
            '22' => '22 Премия за управление проектом, руб.',
            '23' => '23 Разработка ПСД (нч х ставка), руб.',
            '24' => '24 Разработка КД (принц. монт. схемы, РЭ), руб.',
            '25' => '25 Разработка ПО (контроллер, панель), руб.',
            '26' => '26 Сборка оборудования с упаковкой, руб.',
            '27' => '27 Тестирование заводское, руб.',
            '28' => '28 СМР/ШМР, руб.',
            '29' => '29 ПНР, руб.',
            '30' => '30 ПНР ПО, руб.',
            '31' => '31 Техобслуживание/ ремонт, руб.',
            '32' => '32 Затраты на управление работами, руб.',
            '33' => '33 ИТОГО по работам',
            '34' => '34 Субподрядные работы (плюс 2,4% / 6,6% генподряд), руб.',
            '35' => '35 Субподрядные материалы, руб.',
            '36' => '36 Затраты на управление работами',
            '37' => '37 ИТОГО по субподрядам, руб.',
            '38' => '38 ТЗР доставки до объекта, руб.',
            '39' => '39 Командировочные (транспортные + продвижение + суточные), руб.',
            '40' => '40 Связь, руб.',
            '41' => '41 Прочие (с расшифровкой), руб.',
            '42' => '42 ИТОГО прочие, руб.',
            '43' => '43 Планируемая прибыль (стабфонд), руб.',
            '44' => '44 Планируемая добавленная стоимость (НЧТП), руб.',
            '45' => '45 Рентабельность по стабфонду, %',
        ],
        'product_col' => []
    ];

    // Подготовка источника данных
    $nodes = isset($saved_schema['nodes']) ? $saved_schema['nodes'] : [];

    $connectionsParams = isset($saved_schema['connections']) ? $saved_schema['connections'] : [];
    $connections = [];
    foreach($connectionsParams as $connectionsParam){
        $connections[] = $connectionsParam['params'];
    }

    $other = isset($saved_schema['other']) ? $saved_schema['other'] : [];

    // Утилиты
    $makeOptionsStr = function($product) {
        $str = '';

        if (!empty($product['product']['product_option'])) {
            foreach($product['product']['product_option'] as $option) {
                $description = isset($option['get_name']['description']) ? $option['get_name']['description'] : null;

                if ($description) {
                    $description = str_replace('['. $option['get_name']['key'] .']', $option['value'], $description);
                    $str .= $description;
                } else {
                    $name = isset($option['get_name']['name']) ? $option['get_name']['name'] : '';
                    $val  = isset($option['value']) ? $option['value'] : '';
                    $str .= trim($name) . ':' . trim($val) . ', ';
                }
            }
        }

        if (!empty($product['product']['price_rules_applied'])) {
            foreach($product['product']['price_rules_applied'] as $rules_key => $rules_value) {
                $str .= trim($rules_value['rule_name']) . ', ';
            }
        }

        $str = rtrim($str, ', ');
        return $str;
    };

    // параметры расчёта
    $reserve      = isset($pay_params['reserve']) ? floatval($pay_params['reserve']) : 0.0;
    $marketing    = isset($pay_params['marketing']) ? floatval($pay_params['marketing']) : 0.0;
    $marketing_cf = isset($pay_params['marketing_coef']) ? floatval($pay_params['marketing_coef']) : 0.0;
    $nds_percent  = isset($pay_params['nds']) ? floatval($pay_params['nds']) : 0.0;

    $rowIndex = 0;

    foreach(array_merge($nodes, $connections, $other) as $item) {
        if (!isset($item['product']) || !isset($item['product']['id'])) {
            continue;
        }

        $p = $item['product'];
        $pid = $p['hash'] ?? $p['id'];
    
        $name        = isset($p['name']) ? $p['name'] : '';
        $description = isset($p['description']) ? $p['description'] : '';
        $manufacturer = isset($p['manufacturer']) ? $p['manufacturer'] : '';
        if($manufacturer == 'Заказчик') continue;
        $price       = isset($p['price']) ? floatval($p['price']) : 0.0;

        $kd = isset($p['engineering']['КД']) ? floatval($p['engineering']['КД']) : 0.0;
        $kd = $kd * $saved_schema['engineering']['КД'];

        $po = isset($p['engineering']['ПО']) ? floatval($p['engineering']['ПО']) : 0.0;
        $po = $kd * $saved_schema['engineering']['ПО'];

        $smr_shmr = isset($p['engineering']['СМР/ШМР']) ? floatval($p['engineering']['СМР/ШМР']) : 0.0;
        $smr_shmr = $smr_shmr * $saved_schema['engineering']['СМР/ШМР'];

        $pnr_po = isset($p['engineering']['ПНР ПО']) ? floatval($p['engineering']['ПНР ПО']) : 0.0;
        $pnr_po = $pnr_po * $saved_schema['engineering']['ПНР ПО'];

        $pir = isset($p['engineering']['ПИР']) ? floatval($p['engineering']['ПИР']) : 0.0;
        $pir = $pir * $saved_schema['engineering']['ПИР'];

        $assembly = isset($p['engineering']['Сборка']) ? floatval($p['engineering']['Сборка']) : 0.0;
        $assembly = $assembly * $saved_schema['engineering']['Сборка'];

        $mounting = isset($p['engineering']['Монтаж']) ? floatval($p['engineering']['Монтаж']) : 0.0;
        $mounting = $mounting * $saved_schema['engineering']['Монтаж'];

        $tkp_eng = isset($p['engineering']['ТКП']) ? floatval($p['engineering']['ТКП']) : 0.0;
        $tkp_eng = $tkp_eng * $saved_schema['engineering']['ТКП'];

        $psd = isset($p['engineering']['ПСД']) ? floatval($p['engineering']['ПСД']) : 0.0;
        $psd = $psd * $saved_schema['engineering']['ПСД'];

        $pnr = isset($p['engineering']['ПНР']) ? floatval($p['engineering']['ПНР']) : 0.0;
        $pnr = $pnr * $saved_schema['engineering']['ПНР'];

        $currency_val = isset($p['currency_val']) ? floatval($p['currency_val']) : 0.0;
        $delivery = isset($p['delivery']) ? floatval($p['delivery']) : 0.0;

        $oprionString = $makeOptionsStr($item);

        if (isset($table['product_col'][$pid])) {
            $table['product_col'][$pid][4] += 1;

            $qty = $table['product_col'][$pid][4];
            $table['product_col'][$pid][6] = $qty * $table['product_col'][$pid][5];

            $disc = $table['product_col'][$pid][7];

            $table['product_col'][$pid][8] = $table['product_col'][$pid][6] - ($table['product_col'][$pid][6] * ($disc / 100));

            continue;
        }

        $rowIndex++;

        $table['product_col'][$pid] = [];

        $table['product_col'][$pid][0] = $rowIndex;
        $table['product_col'][$pid][1] = $manufacturer;
        $table['product_col'][$pid][2] = $name;
        $table['product_col'][$pid][3] = $description;
        $table['product_col'][$pid][4] = 1;
        $table['product_col'][$pid][10] = 1;

        $table['product_col'][$pid][18] = ($price * $currency_val) + $delivery;
        $table['product_col'][$pid][19] = $table['product_col'][$pid][18] * ($reserve / 100);
        $table['product_col'][$pid][20] = 0;
        $table['product_col'][$pid][21] = (
            $table['product_col'][$pid][18] +
            $table['product_col'][$pid][19] +
            $table['product_col'][$pid][20]
        );

        $table['product_col'][$pid][12] = 0;
        $table['product_col'][$pid][13] = 0;
        $table['product_col'][$pid][14] = 0;
        $table['product_col'][$pid][16] = 0;
        $table['product_col'][$pid][38] = 0;
        $table['product_col'][$pid][39] = 0;
        $table['product_col'][$pid][40] = 0;
        $table['product_col'][$pid][41] = 0;
        $table['product_col'][$pid][42] = (
            $table['product_col'][$pid][38] +
            $table['product_col'][$pid][39] +
            $table['product_col'][$pid][40] +
            $table['product_col'][$pid][41]
        );

        $table['product_col'][$pid][22] = 0;
        $table['product_col'][$pid][23] = $psd;
        $table['product_col'][$pid][24] = $kd;
        $table['product_col'][$pid][25] = $po;
        $table['product_col'][$pid][26] = $assembly;
        $table['product_col'][$pid][27] = 0;
        $table['product_col'][$pid][28] = $smr_shmr;
        $table['product_col'][$pid][29] = $pnr;
        $table['product_col'][$pid][30] = $pnr_po;
        $table['product_col'][$pid][31] = 0;
        $table['product_col'][$pid][32] = 0;

        $table['product_col'][$pid][33] = (
            $table['product_col'][$pid][22] +
            $table['product_col'][$pid][23] +
            $table['product_col'][$pid][24] +
            $table['product_col'][$pid][25] +
            $table['product_col'][$pid][26] +
            $table['product_col'][$pid][27] +
            $table['product_col'][$pid][28] +
            $table['product_col'][$pid][29] +
            $table['product_col'][$pid][30] +
            $table['product_col'][$pid][31] +
            $table['product_col'][$pid][32]
        );

        $table['product_col'][$pid][34] = 0;
        $table['product_col'][$pid][35] = 0;
        $table['product_col'][$pid][36] = 0;
        $table['product_col'][$pid][37] = (
            $table['product_col'][$pid][34] +
            $table['product_col'][$pid][35] +
            $table['product_col'][$pid][36]
        );

        $table['product_col'][$pid][11] = (
            $table['product_col'][$pid][21] +
            $table['product_col'][$pid][33] +
            $table['product_col'][$pid][37] +
            $table['product_col'][$pid][42]
        );

        $coll_15 = (
            $table['product_col'][$pid][11] +
            $table['product_col'][$pid][12] +
            $table['product_col'][$pid][14] +
            $table['product_col'][$pid][16]
        ) - $table['product_col'][$pid][13];

        $table['product_col'][$pid][15] = ($coll_15 * $marketing) + ($coll_15 * $marketing * $marketing_cf);

        $table['product_col'][$pid][17] = (
            $table['product_col'][$pid][11] +
            $table['product_col'][$pid][12] +
            $table['product_col'][$pid][14] +
            $table['product_col'][$pid][15] +
            $table['product_col'][$pid][16]
        ) - $table['product_col'][$pid][13];

        $table['product_col'][$pid][5] = $price * $currency_val;
        $table['product_col'][$pid][6] = $table['product_col'][$pid][4] * $table['product_col'][$pid][5];
        $table['product_col'][$pid][7] = 0.0;
        $table['product_col'][$pid][8] = $table['product_col'][$pid][6] - ($table['product_col'][$pid][6] * ($table['product_col'][$pid][7] / 100));

        $table['product_col'][$pid][43] = $table['product_col'][$pid][8] - $table['product_col'][$pid][17];
        $table['product_col'][$pid][44] = $table['product_col'][$pid][33] - $table['product_col'][$pid][43];
        $table['product_col'][$pid][45] = ($table['product_col'][$pid][8] == 0) ? 0 : ($table['product_col'][$pid][43] / $table['product_col'][$pid][8]);
    }

    $total = 0;
    foreach ($table['product_col'] as $pc) {
        $total += isset($pc[8]) ? floatval($pc[8]) : 0.0;
    }

    $nds = $total * ($nds_percent / 100);
    $total_nds = $total + $nds;

    $form->pay_params['resault_total'] = $total;
    $form->pay_params['resault_nds'] = $nds;
    $form->pay_params['resault_total_nds'] = $total_nds;
@endphp

                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            @foreach ($table['col'] as $key => $col)
                                <th @if($key == '3') class="col-name-th" @endif>{{ $col }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($table['product_col'] as $prod_col)
                        <tr>
                            @foreach($table['col'] as $key => $table_col)
                                @if(isset($prod_col[$key]))
                                    @if($key == '3')
                                    @php $fullText = strip_tags($prod_col[$key]); @endphp
                                    <td x-data="{ open: false, tooltip: {{ json_encode($fullText) }} }"
                                        @click="open = !open"
                                        :class="open ? 'col-name-cell col-name-expanded' : 'col-name-cell'"
                                        :title="open ? '' : tooltip">
                                        <span class="col-name-text">{!! $prod_col[$key] !!}</span>
                                        <i class="bi col-name-icon ms-1"
                                           :class="open ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                                    </td>
                                    @else
                                    <td>{!! $prod_col[$key] !!}</td>
                                    @endif
                                @else
                                <td class="text-muted">{{ $key }}</td>
                                @endif
                            @endforeach
                        </tr>
                        @endforeach

                        <tr class="table-light fw-semibold">
                            <td colspan="3"></td>
                            <td>Итого:</td>
                            <td colspan="4"></td>
                            <td class="text-end">{{ number_format((int)$total, 0, '.', ' ') }}</td>
                            <td colspan="{{ count($table['col']) - 9 }}"></td>
                        </tr>
                        <tr class="table-light fw-semibold">
                            <td colspan="3"></td>
                            <td>НДС:</td>
                            <td colspan="4"></td>
                            <td class="text-end">{{ number_format((int)$nds, 0, '.', ' ') }}</td>
                            <td colspan="{{ count($table['col']) - 9 }}"></td>
                        </tr>
                        <tr class="table-success fw-bold">
                            <td colspan="3"></td>
                            <td>Итого с учётом НДС:</td>
                            <td colspan="4"></td>
                            <td class="text-end">{{ number_format((int)$total_nds, 0, '.', ' ') }}</td>
                            <td colspan="{{ count($table['col']) - 9 }}"></td>
                        </tr>
                    </tbody>
                </table>

            </div>{{-- /.calc-table-wrap --}}
        </div>
    </div>

    @endif
</div>
