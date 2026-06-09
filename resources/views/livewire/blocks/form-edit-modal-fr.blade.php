<div class="modal fade" id="editModalFR" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog" style="--bs-modal-width:800px !important">
        <div class="modal-content">
            <div class="modal-header" wire:ignore>
                <h5 class="modal-title" id="modal-title-node-fr"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body" wire:loading.class="opacity-50">

                <hr />
                <div class="row">
                    <div class="col-4">

                        <div style="width: 100%; text-align: center;"><b>Опции</b></div>

                        @php 
                            $form_data = [
                                'material_trans'                => 'select',
                                'ip'                            => 'select',
                                'sync_to_grid'                  => 'select',
                                'power_cell_bypass'             => 'select',
                                'interface'                     => 'select',
                                'precharge'                     => 'select',
                                'service_vfd'                   => 'select',
                                'plc_syn'                       => 'select',
                                'plc_pt_100'                    => 'select',
                                'bypass_vfd'                    => 'select',
                                'section_in_out'                => 'select',
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
                                                <?php 
                                                    // отключать "Сервисный интерфейс" серии "Стандарт" и "Минпромторг"
                                                    if ($key == 'service_vfd') {
                                                        if ($getData['vfd_series'] == 'Стандарт' || $getData['vfd_series'] == 'Стандарт (Минпромторг)') {
                                                            echo 'disabled';
                                                        }
                                                    }
                                                    //
                                                    if ($key == 'plc_syn') {
                                                        if ($getData['motor_type'] == 'A') {
                                                            echo 'disabled';
                                                        }
                                                    }
                                                ?>

                                            >
                                
                                                <?php
                                                    // 
                                                    if ($key == 'service_vfd') {
                                                        ($getData['vfd_series'] == 'Стандарт' || $getData['vfd_series'] == 'Стандарт (Минпромторг)') ? $disabled = '' : $disabled = 'disabled';
                                                        echo '<option value="" ' . $disabled . '>---</option>';
                                                    }
                                                ?>

                                                @foreach ($product_filter['fields'] as $field)
                                                    @if ($product_filter['key'] == 'power_cell_bypass' && $field == 'Электронный')
                                                    @else
                                                    <option value="{{$field}}">{{$field}}</option>
                                                    @endif
                                                @endforeach
                                
                                            </select>
                                            
                                            @if ($product_filter['key'] == 'power_cell_bypass')
                                            <div class="alert alert-warning" role="alert" wire:show="getData.power_cell_bypass == 'Механический'" style="font-size: 11px;">
                                                Опция байпас неисправной силовой ячейки (электронный) предоставляется по запросу. Обратитесь в техническую поддержку продукта.
                                            </div>
                                            @endif

                                            @if ($product_filter['key'] == 'ip')
                                            <div class="alert alert-info" role="alert" wire:show="getData.ip == '42'" style="font-size: 11px;">
                                                Без учета цены воздуховода.
                                            </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                        
                    </div>

                    <div class="col-4">
                        <div style="width: 100%; text-align: center;"><b>Общая информация</b></div>

                        <div class="mb-3">
                            <label for="modal-input1-fr" class="form-label">Название</label>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input1-fr" class="form-control" placeholder="Название или тип">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="modal-input2-fr" class="form-label">Дополнительно</label>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input2-fr" class="form-control" placeholder="Дополнительно">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="getData.manufacturer.fr" class="form-label">Завод изготовитель</label>
                            <select
                                wire:key="getData.manufacturer.fr"
                                wire:model.defer="getData.manufacturer"
                                id="getData.manufacturer.fr"
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
                                'vfd_series'                    => 'select',
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
                                <option value="S">Синхронный</option>
                            </select>
                        </div>
                    
                        <div class="mb-3">
                            <label for="getData.v_output" class="form-label">Номинальное напряжение,В</label>
                            <select
                                wire:key="getData.v_output"
                                wire:model.defer="getData.v_output"
                                id="getData.v_output"
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
            <div class="modal-body" wire:show="message_success">
                <div class="alert alert-success" role="alert">
                    {!! $message_success !!}
                </div>
            </div>

            <div class="modal-body" wire:show="message_error">
                <div class="alert alert-danger" role="alert">
                    {!! $message_error !!}
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger me-auto" onclick="deleteModalTarget()" data-bs-dismiss="modal" wire:loading.attr="disabled">Удалить</button>
                <button type="submit" class="btn btn-primary" onclick="saveModal()" wire:loading.attr="disabled">Сохранить</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
