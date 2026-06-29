<div class="modal fade" id="editModalUPP" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog" style="--bs-modal-width:800px !important">
        <div class="modal-content">
            <div class="modal-header" wire:ignore>
                <h5 class="modal-title" id="modal-title-node-upp" x-ref="modalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body" wire:loading.class="opacity-50">

                <hr />
                <div class="row">
                    <div class="col-4">

                        <div style="width: 100%; text-align: center;"><b>Опции</b></div>

                        @php 
                            $form_data = [
                                'v_control'                     => 'select',
                                'ip'                            => 'select',
                                'bypass_breaker'                => 'select',
                                //'service_smv'                   => 'select',
                                'interface'                     => 'select',
                                //'motor_reverse'                 => 'select',
                                //'cascade'                       => 'select',
                                'line_switch'                   => 'select',
                                'wsk'                           => 'select',
                                //'count_power_thyristors'        => 'select',
                            ];
                        @endphp

                        @foreach ($form_data as $key => $value)
                            @if ($value = 'select')
                                @foreach ($product_filter_select as $product_filter) 
                                    @if ($product_filter['key'] == $key)
                                        <div class="mb-3" wire:key="getDataDiv-{{$key}}">
                                        
                                            <label for="getData-{{$key}}" class="form-label" style="font-size: 11px;">{{$product_filter['name']}}.</label>
                                            <select
                                                wire:key="getData-{{$key}}"
                                                wire:model.defer="getData.{{$key}}"
                                                id="getData-{{$key}}"
                                                class="form-select"
                                                wire:change.debounce.500ms="updateData('{{$key}}', $event.target.value)"
                                                style="font-size: 11px;"
                                            >
                                                @foreach ($product_filter['fields'] as $field)
                                                    @if ($product_filter['key'] == 'power_cell_bypass' && $field == 'Электронный')
                                                    @elseif ($product_filter['key'] == 'line_switch')
                                                    <?php $disabled = ''; ?>
                                                    {{-- включение и отключение полей в модальном окне при открытии модального окна УПП --}}
                                                    <?php if ($modal_title == 'Редактировать УПП' && $field == 'Да') $disabled = 'disabled'; ?>
                                                    <?php if ($modal_title == 'Редактировать УПП лин.' && $field == 'Нет') $disabled = 'disabled'; ?>

                                                    <option value="{{$field}}" {{ $disabled }}>{{$field}}</option>
                                                    @else
                                                    <option value="{{$field}}">{{$field}}</option>
                                                    @endif
                                                @endforeach
                                
                                            </select>

                                            <?php
                                                if ($key == 'line_switch') {
                                            ?>
                                                <!-- Блок alert показывается, только если совпадают оба условия -->
                                                <div class="alert alert-warning" role="alert" style="font-size: 11px;"
                                                    x-show="$wire.getData.line_switch == 'Да' && (document.getElementById('modal-title-node-upp')?.textContent || '').trim() == 'Редактировать УПП'" 
                                                    x-cloak>
                                                    Замените блок “Устройство плавного пуска” на блок “Устройство плавного пуска с линейным”
                                                </div>

                                                <div class="alert alert-warning" role="alert" style="font-size: 11px;"
                                                    x-show="$wire.getData.line_switch == 'Нет' && (document.getElementById('modal-title-node-upp')?.textContent || '').trim() == 'Редактировать УПП лин.'" 
                                                    x-cloak>
                                                    Замените блок “Устройство плавного пуска с линейным” на блок “Устройство плавного пуска”
                                                </div>
                                            <?php
                                                }
                                            ?>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        
                    </div>

                    <div class="col-4">
                        <div style="width: 100%; text-align: center;"><b>Общая информация</b></div>

                        <div class="mb-3">
                            <label for="modal-input1-upp" class="form-label">Название</label>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input1-upp" class="form-control" placeholder="Название или тип">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="modal-input2-upp" class="form-label">Дополнительно</label>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input2-upp" class="form-control" placeholder="Дополнительно">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="getData.manufacturer.upp" class="form-label">Завод изготовитель</label>
                            <select
                                wire:key="getData.manufacturer.upp"
                                wire:model.defer="getData.manufacturer"
                                id="getData.manufacturer.upp"
                                class="form-select"
                                wire:change.debounce.500ms="updateValueManufacturer($event.target.value)"
                            >
                                @foreach($product_manufacturer_select as $manufacturer)
                                    <?php 
                                        $selected = '';
                                        if($manufacturer['name'] == $getData['manufacturer']) {
                                            $selected = 'selected';
                                        }
                                    ?>
                                    <option value="{{ $manufacturer['name'] }}" {{$selected}}>{{ $manufacturer['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        @php 
                            $form_data = [
                                'smv_series'                    => 'select',
                            ];
                        @endphp

                        @foreach ($form_data as $key => $value)
                            @if ($value = 'select')
                                @foreach ($product_filter_select as $product_filter) 
                                    @if ($product_filter['key'] == $key)
                                        <div class="mb-3" wire:key="getDataDiv-{{$key}}">
                                        
                                            <label for="getData-{{$key}}" class="form-label" style="font-size: 11px;">{{$product_filter['name']}}.</label>
                                            <select
                                                wire:key="getData-{{$key}}"
                                                wire:model.defer="getData.{{$key}}"
                                                id="getData-{{$key}}"
                                                class="form-select"
                                                wire:change.debounce.500ms="updateData('{{$key}}', $event.target.value)"
                                                style="font-size: 11px;"
                                            >
                                                @foreach ($product_filter['fields'] as $field)
                                                    @if ($product_filter['key'] == 'power_cell_bypass' && $field == 'Электронный')
                                                    @else
                                                    <option value="{{$field}}">{{$field}}</option>
                                                    @endif
                                                @endforeach
                                
                                            </select>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach


                        {{-- Правила цены применяются автоматически по опциям продукта --}}

                    </div>

                    <div class="col-4">
                        <div style="width: 100%; text-align: center;"><b>Характеристики электродвигателя</b></div>

                        <div class="mb-3">
                            <label for="getData.motor_type" class="form-label">Тип электродвигателя</label>
                            <select
                                wire:key="getData.motor_type"
                                wire:model.defer="getData.motor_type"
                                id="getData.motor_type" 
                                class="form-select"
                                wire:change.debounce.500ms="updateData('motor_type', $event.target.value)"
                            >
                                <option value="A">Асинхронный</option>
                                <option value="S" disabled>Синхронный</option>
                            </select>
                            <div class="alert alert-info" role="alert" style="font-size: 11px;" wire:show="getData.motor_type == 'A'">
                                <span>
                                    УПП для синхронного двигателя по запросу
                                </span>
                            </div>
                            
                        </div>

                        <div class="mb-3">
                            <label for="getData.v_input" class="form-label">Входное напряжение, В</label>
                            <select
                                wire:key="getData.v_input"
                                wire:model.defer="getData.v_input"
                                id="getData.v_input"
                                class="form-select"
                                wire:change.debounce.500ms="updateValueVoltage($event.target.value)"
                            >
                                <option value="6000">6000</option>
                                <option value="10000">10000</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="getData.p_output" class="form-label">Мощность,кВт</label>
                            <input
                                type="number"
                                wire:key="getData.p_output"
                                wire:model.defer="getData.p_output"
                                id="getData.p_output"
                                class="form-control"
                                placeholder="0"
                                wire:change.debounce.500ms="updateValuePower($event.target.value)"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="getData.nominalnyi_tok_ed_a" class="form-label">Номинальный ток, А</label>
                            <input
                                type="number"
                                wire:key="getData.nominalnyi_tok_ed_a"
                                wire:model.defer="getData.nominalnyi_tok_ed_a"
                                id="getData.nominalnyi_tok_ed_a"
                                class="form-control"
                                placeholder="0"
                                wire:change.debounce.500ms="updateValueCurent($event.target.value)"
                            >
                        </div>
                        

                        <div class="mb-3">
                            <label for="getData.kpd" class="form-label">КПД,%</label>
                            <small class="text-muted">Значение должно быть от 0 до 100</small>
                            <input
                                type="number"
                                wire:key="getData.kpd"
                                wire:model.defer="getData.kpd"
                                id="getData.kpd"
                                class="form-control"
                                placeholder="0"
                                wire:change.debounce.500ms="updateValueKpd($event.target.value)"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="getData.cos_phi" class="form-label">Cos φ</label>
                            <small class="text-muted">Значение должно быть от 0 до 1</small>
                            <input
                                type="number"
                                wire:key="getData.cos_phi"
                                wire:model.defer="getData.cos_phi"
                                id="getData.cos_phi"
                                class="form-control"
                                placeholder="0" 
                                wire:change.debounce.500ms="updateValueCosPhi($event.target.value)"
                                step="0.1"
                            >
                        </div>

                        <div class="alert alert-info" role="alert" style="font-size: 11px;">
                            <span>
                                Для ручного ввода тока и мощности должно быть КПД = 0, Cos φ = 0
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-body" wire:loading>
                Загрузка фильтра ...
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert" wire:show="message_success">
                    {!! $message_success !!}
                </div>
                <div class="alert alert-danger" role="alert" wire:show="message_error">
                    {!! $message_error !!}
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger me-auto" onclick="deleteModalTarget()" data-bs-dismiss="modal" wire:loading.attr="disabled">Удалить</button>
                <button type="submit" class="btn btn-primary" onclick="saveModal()" wire:loading.attr="disabled">Применить</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('editModalUPP').addEventListener('shown.bs.modal', () => {
            // включение и отключение полей в модальном окне при открытии модального окна УПП
            Livewire.dispatch('uppModalOpened', {
                data : {
                    modalTitle: document.getElementById('modal-title-node-upp').textContent.trim(),
                    modalInput1: document.getElementById('modal-input1-upp').value.trim(),
                    modalInput2: document.getElementById('modal-input2-upp').value.trim()
                }
            });
        });
    </script>
</div>
