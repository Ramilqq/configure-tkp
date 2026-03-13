<div>
    <div class="container">
        @if(!isset($saved_schema['nodes']))
    
            <p>Нет данных для создания ТКП</p>
            <p>
                <a href="{{route('tkp.contact.edit', ['id' => $id, 'tkp_version' => $tkp_version])}}">Продолжить</a>
            </p>

        @else
        
        <div class="row">
            <div class="col bg-light rounded mb-1">
                <b><span>Страницы проекта:</span></b>
                <div class="btn-groups">
                    <a href="{{ route('tkp.contact.edit', ['id' => $id, 'tkp_version' => $tkp_version]) }}" class="btn btn-success btn me-1" target="_blank" title="Изменить страницу 'Контактная информация'"><i class="bi bi-person-lines-fill"></i></a>
                    <a href="{{ route('tkp.sheme.edit', ['id' => $id, 'tkp_version' => $tkp_version]) }}" class="btn btn-success btn me-1" target="_blank" title="Изменить страницу 'Схема'"><i class="bi bi-diagram-3-fill"></i></a>
                    <a href="{{ route('tkp.delivery.edit', ['id' => $id, 'tkp_version' => $tkp_version]) }}" class="btn btn-success btn me-1" target="_blank" title="Изменить страницу 'Доставка'"><i class="bi bi-truck-front-fill"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            
            <div class="col-4 rounded">
                <div class="row">
<!-- блок с ценами -->
                    <div class="col-12 bg-light rounded">
                        <b><span>Дополнительные параметры:</span></b>
                        <div class="row g-3 align-items-center pb-1">
                            <div class="col-12">
                                Обновить цены по текущему курсу:
                                <button title="Обновить" class="btn btn-primary btn-sm"
                                    wire:click="currency()"
                                ><i class="bi bi-arrow-clockwise"></i></button>
                            </div>
                        </div>

                        <div class="row g-3 align-items-center pb-1">
                            <div class="col-4">
                                Расходы на <br />продвижение (%):
                            </div>
                            <div class="col-8">
                                <input type="text" id="pay_params.marketing" class="form-control" wire:model.lazy="pay_params.marketing">
                            </div>
                        </div>

                        <div class="row g-3 align-items-center pb-1">
                            <div class="col-4">
                                НДС:
                            </div>
                            <div class="col-8">
                                <select class="form-select" wire:model.lazy="pay_params.nds" id="pay_params.nds" >
                                    <option value="0">0%</option>
                                    <option value="18">18%</option>
                                    <option value="20">20%</option>
                                    <option value="22">22%</option>
                                </select>
                            </div>
                        </div>

                        <div class="row g-3 align-items-center pb-1">
                            <div class="col-4">
                                Резерв на <br />изменение (%):
                            </div>
                            <div class="col-8">
                                <input type="text" id="pay_params.reserve" class="form-control" wire:model.lazy="pay_params.reserve">
                            </div>
                        </div>
                    </div>
                    
                    <br />

<!-- блок с версиями -->
                    <div class="col-12 bg-light my-1 px-1 rounded">
                        
                        <!--div class="row g-3 align-items-center pb-1">
                            <div class="col-4">
                                Версии:
                            </div>
                            <div class="col-8">
                                <select class="form-select">
                                    <option selected>Open this select menu</option>
                                    <option value="1">One</option>
                                    <option value="2">Two</option>
                                    <option value="3">Three</option>
                                </select>
                            </div>
                        </div-->
                        <div class="row g-3 align-items-center pb-1">
                            <div class="col-4">
                                Комментарий<br />к версии:
                            </div>
                            <div class="col-8">
                                <input type="text" id="form.comments" class="form-control" wire:model.lazy="form.comments">
                            </div>
                        </div>

                    </div>

<!-- блок с версиями -->
                    <div class="col-12 bg-light my-1 px-1 rounded">
                        
                        <div class="row g-3 align-items-center pb-1">
                            <a target="_blank" href="{{route('tkp.pdf.show', ['id' => $id, 'tkp_version' => $tkp_version])}}" type="button" class="btn btn-success">Открыть PDF</a>
                        </div>
                        <div class="row g-3 align-items-center pb-1">
                        
                        </div>

                    </div>

                </div>
            </div>
            <div class="col bg-light ms-1 rounded">
                <b><span>Список продуктов:</span></b>
                <div class="btn-groups">
                    <livewire:tkp.modal.add-product :tkp_version="$tkp_version" :banks="$banks"/>
                </div>
                
                <hr />

                <table class="table">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Наименование</th>
                        <th scope="col">Цена</th>
                        <th scope="col">Валюта</th>
                        <th scope="col">Курс</th>
                        <th scope="col">Доставка</th>
                        <th scope="col">Кнопки</th>
                        </tr>
                    </thead>
                    <tbody>
                       @forelse($saved_schema['nodes'] as $nodes)
                        @php 
                                if (!isset($nodes['product'])) continue;
                        @endphp
                            <tr>
                                <th scope="row">{{$nodes['product']['id']}}</th>
                                <td>{{$nodes['product']['name']}}</td>
                                <td>{{$nodes['product']['price']}}</td>
                                <td>{{$nodes['product']['currency']}}</td>
                                <td>{{$nodes['product']['currency_val']}}</td>
                                <td>{{$nodes['product']['delivery']}} RUB</td>
                                <td>
                                    <!-- кнопка изменить товар -->
                                    <button title="Изменить продукт" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductForm"
                                        @click="$dispatch('addProductOpenForm', {product_id : '{{$nodes['id']}}' })"
                                    ><i class="bi bi-pencil-square"></i></button>
                                </td>
                            </tr>
                        @empty
                        @endforelse

                        @forelse($saved_schema['connections'] as $nodes)
                            @if(isset($nodes['params']['product']))
                                <tr>
                                    <th scope="row">{{$nodes['params']['product']['id']}}</th>
                                    <td>{{$nodes['params']['product']['name']}}</td>
                                    <td>{{$nodes['params']['product']['price']}}</td>
                                    <td>{{$nodes['params']['product']['currency']}}</td>
                                    <td>{{$nodes['params']['product']['currency_val']}}</td>
                                    <td>{{$nodes['params']['product']['delivery']}} RUB</td>
                                    <td>{{$nodes['params']['product']['id']}}</td>
                                </tr>
                            @endif
                        @empty
                        @endforelse
                        
                        @if(isset($saved_schema['other']))
                            @forelse($saved_schema['other'] as $nodes)
                                <tr>
                                    <th scope="row">{{$nodes['product']['id']}}</th>
                                    <td>{{$nodes['product']['name']}}</td>
                                    <td>{{$nodes['product']['price']}}</td>
                                    <td>{{$nodes['product']['currency']}}</td>
                                    <td>{{$nodes['product']['currency_val']}}</td>
                                    <td>{{$nodes['product']['delivery']}} RUB</td>
                                    <td>
                                        <!-- кнопка изменить товар -->
                                        <button title="Изменить продукт" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addProductForm"
                                            @click="$dispatch('addProductOpenForm', {product_id : '{{$nodes['id']}}' })"
                                        ><i class="bi bi-pencil-square"></i></button>
                                        
                                        <!-- кнопка удалить товар -->
                                        <button title="Удалить продукт" class="btn btn-danger btn-sm"
                                            @click="$dispatch('addProductRemove', {product_id : '{{$nodes['id']}}' })"
                                        ><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            @empty
                            @endforelse
                        @endif
                    </tbody>
                </table>

            </div>
            




        </div>

        <div class="row">
            <div class="col-12 m-1 col bg-light m-1 p-1 rounded" style="display: inline-table;">
                
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
                // если get_name может отсутствовать — проверяем
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
    $reserve      = isset($pay_params['reserve']) ? floatval($pay_params['reserve']) : 0.0;            // %
    $marketing    = isset($pay_params['marketing']) ? floatval($pay_params['marketing']) : 0.0;        // коэффициент/%
    $marketing_cf = isset($pay_params['marketing_coef']) ? floatval($pay_params['marketing_coef']) : 0.0;
    $nds_percent  = isset($pay_params['nds']) ? floatval($pay_params['nds']) : 0.0;                    // %

    // Свод по товарам: объединяем по product_id
    $rowIndex = 0; // для колонки 0 (N пп)
    foreach(array_merge($nodes, $connections, $other) as $item) {
        if (!isset($item['product']) || !isset($item['product']['id'])) {
            continue;
        }

        $p = $item['product'];
        $pid = $p['id'];

        // Достаём базовые значения
        $name        = isset($p['name']) ? $p['name'] : '';
        $description = isset($p['description']) ? $p['description'] : '';
        $manufacturer = isset($p['manufacturer']['name']) ? $p['manufacturer']['name'] : '';
        $price       = isset($p['price']) ? floatval($p['price']) : 0.0;

        // инжиниринг
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

        $tkp = isset($p['engineering']['ТКП']) ? floatval($p['engineering']['ТКП']) : 0.0;
        $tkp = $tkp * $saved_schema['engineering']['ТКП'];

        $psd = isset($p['engineering']['ПСД']) ? floatval($p['engineering']['ПСД']) : 0.0;
        $psd = $psd * $saved_schema['engineering']['ПСД'];

        $pnr = isset($p['engineering']['ПНР']) ? floatval($p['engineering']['ПНР']) : 0.0;
        $pnr = $pnr * $saved_schema['engineering']['ПНР'];

        // курс
        $currency_val = isset($p['currency_val']) ? floatval($p['currency_val']) : 0.0;;

        // доставка
        $delivery = isset($p['delivery']) ? floatval($p['delivery']) : 0.0;

        $oprionString = $makeOptionsStr($item);

        $rulesId = '';
        if (!empty($item['product']['price_rules_applied'])) {
            foreach($item['product']['price_rules_applied'] as $rules_key => $rules_value) {
                $rulesId .= trim($rules_value['rule_key']) . ', ';
            }
        }

        $pid = $pid . $rulesId;
        
        

        // Если товар уже есть — наращиваем количество и пересчитываем зависящие колонки
        if (isset($table['product_col'][$pid])) {
            // +1 к количеству
            $table['product_col'][$pid][4] += 1;

            // Пересчёт цен, зависящих от количества
            // 5 — стоимость за единицу (руб. без НДС) — НЕ меняется
            // 6 — стоимость = qty * price_per_unit
            $qty = $table['product_col'][$pid][4];
            $table['product_col'][$pid][6] = $qty * $table['product_col'][$pid][5];

            // 7 — скидка, % (оставляем как есть; при необходимости можно задавать на уровне позиции)
            $disc = $table['product_col'][$pid][7];

            // 8 — общая сумма со скидкой
            $table['product_col'][$pid][8] = $table['product_col'][$pid][6] - ($table['product_col'][$pid][6] * ($disc / 100));

            // Прочие агрегаты, если они должны зависеть от количества
            // Остальные поля (11..17.. и т.д.) оставляем как в исходной логике (за позицию), при желании можно масштабировать.

            continue;
        }

        // --- Создаём новую строку товара ---
        $rowIndex++;
        $table['product_col'][$pid] = [];

        // 0 — № пп
        $table['product_col'][$pid][0] = $rowIndex;

        // 1 — Завод-изготовитель (в твоих данных — имя?)
        $table['product_col'][$pid][1] = $manufacturer;

        // 2 — Тип/марка (в твоих данных — описание?)
        $table['product_col'][$pid][2] = $name;

        // 3 — Наименование опций
        $table['product_col'][$pid][3] = $oprionString;
        //$table['product_col'][$pid][3] = $description;

        // 4 — Кол-во
        $table['product_col'][$pid][4] = 1;

        // 10 — Коэффициент продажной цены
        $table['product_col'][$pid][10] = 1;

        // 18 — Плановая себестоимость ед. с учётом курса
        $table['product_col'][$pid][18] = ($price * $currency_val) + $delivery;

        // 19 — Резерв на изменения по ед.
        $table['product_col'][$pid][19] = $table['product_col'][$pid][18] + ($table['product_col'][$pid][18] * ($reserve / 100));

        // 20 — ТЗР закупки по ед.
        $table['product_col'][$pid][20] = 0;

        // 21 — Итого по закупке по ед.
        $table['product_col'][$pid][21] = (
            $table['product_col'][$pid][18] +
            $table['product_col'][$pid][19] +
            $table['product_col'][$pid][20]
        );

        // Блок прочих расходов (по ед.)
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

        // Работы
        $table['product_col'][$pid][22] = 0; //Премия за управление проектом
        $table['product_col'][$pid][23] = $psd; //Разработка ПСД
        $table['product_col'][$pid][24] = $kd; //Разработка КД
        $table['product_col'][$pid][25] = $po; //Разработка ПО
        $table['product_col'][$pid][26] = $assembly; //Сборка оборудования
        $table['product_col'][$pid][27] = 0; //Тестирование заводское
        $table['product_col'][$pid][28] = $smr_shmr; //СМР/ШМР
        $table['product_col'][$pid][29] = $pnr; //ПНР
        $table['product_col'][$pid][30] = $pnr_po; //ПНР ПО
        $table['product_col'][$pid][31] = 0; //Техобслуживание/ ремонт
        $table['product_col'][$pid][32] = 0; //Затраты на управление работами

        // 33 — Итого по работам (сумма блока 22..32)
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

        // Субподряды
        $table['product_col'][$pid][34] = 0;
        $table['product_col'][$pid][35] = 0;
        $table['product_col'][$pid][36] = 0;
        $table['product_col'][$pid][37] = (
            $table['product_col'][$pid][34] +
            $table['product_col'][$pid][35] +
            $table['product_col'][$pid][36]
        );

        // 11 — Бюджет реализации в объемах (по ед.)
        $table['product_col'][$pid][11] = (
            $table['product_col'][$pid][21] +
            $table['product_col'][$pid][33] +
            $table['product_col'][$pid][37] +
            $table['product_col'][$pid][42]
        );

        // 15 — Расходы на продвижение (по ед.)
        $coll_15 = (
            $table['product_col'][$pid][11] +
            $table['product_col'][$pid][12] +
            $table['product_col'][$pid][14] +
            $table['product_col'][$pid][16]
        ) - $table['product_col'][$pid][13];

        $table['product_col'][$pid][15] = ($coll_15 * $marketing) + ($coll_15 * $marketing * $marketing_cf);

        // 17 — Итого расходы (по ед.)
        $table['product_col'][$pid][17] = (
            $table['product_col'][$pid][11] +
            $table['product_col'][$pid][12] +
            $table['product_col'][$pid][14] +
            $table['product_col'][$pid][15] +
            $table['product_col'][$pid][16]
        ) - $table['product_col'][$pid][13];

        // ---------- Цены/кол-ва ----------
        // 5 — Стоимость единицы, руб. без НДС (UNIT!)
        $table['product_col'][$pid][5] = $price * $currency_val;

        // 6 — Стоимость , руб. без НДС = qty * unit_price
        $table['product_col'][$pid][6] = $table['product_col'][$pid][4] * $table['product_col'][$pid][5];

        // 7 — Скидка, % (по умолчанию 0)
        $table['product_col'][$pid][7] = 0.0;

        // 8 — Общая сумма со скидкой = 6 - скидка
        $table['product_col'][$pid][8] = $table['product_col'][$pid][6] - ($table['product_col'][$pid][6] * ($table['product_col'][$pid][7] / 100));

        // 43 — Планируемая прибыль (стабфонд) = выручка - расходы
        // здесь берём расходы "по ед." и не умножаем на qty; если нужно — умножайте на qty.
        $table['product_col'][$pid][43] = $table['product_col'][$pid][8] - $table['product_col'][$pid][17];

        // 44 — НЧТП = Итого по работам - стабфонд
        $table['product_col'][$pid][44] = $table['product_col'][$pid][33] - $table['product_col'][$pid][43];

        // 45 — Рентабельность = стабфонд / выручка
        $table['product_col'][$pid][45] = ($table['product_col'][$pid][8] == 0) ? 0 : ($table['product_col'][$pid][43] / $table['product_col'][$pid][8]);
    }

    // --- Итоги по таблице ---
    $total = 0;
    foreach ($table['product_col'] as $pc) {
        // суммируем колонку 8 (общая сумма со скидкой, без НДС)
        $total += isset($pc[8]) ? floatval($pc[8]) : 0.0;
    }

    $nds = $total * ($nds_percent / 100);
    $total_nds = $total + $nds;

    $form->pay_params['resault_total'] = $total;
    $form->pay_params['resault_nds'] = $nds;
    $form->pay_params['resault_total_nds'] = $total_nds;
    
    $this->saveParams();
@endphp
            <table class="table">
                <thead>
                    <tr class="table-tr-th">
                    @foreach ($table['col'] as $col)
                        <th>{{$col}}</th>
                    @endForeach
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach ($table['product_col'] as $prod_col)
                    <tr >
                        @foreach($table['col'] as $key => $table_col)
                            @if(isset($prod_col[$key]))
                            <td>{{$prod_col[$key]}}</td>
                            @else
                            <td>{{$key}}</td>
                            @endif
                            
                        @endForeach
                    </tr>
                    @endForeach
                    
                    <tr>
                        <th></th>
                        <td></td>
                        <td></td>
                        <td>Итого:</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$total}}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td></td>
                        <td></td>
                        <td>НДС:</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$nds}}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <th></th>
                        <td></td>
                        <td></td>
                        <td>Итого с учетом НДС:</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{$total_nds}}</td>
                        <td></td>
                    </tr>
                </tbody>

            </table>






            </div>
        </div>
        @endif

    </div>
</div>
