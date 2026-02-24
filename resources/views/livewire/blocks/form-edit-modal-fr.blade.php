<div class="modal fade" id="editModalFR" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog" style="--bs-modal-width:800px !important">
        <div class="modal-content">
            <div class="modal-header" wire:ignore>
                <h5 class="modal-title" id="modal-title-node"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body" wire:loading.class="opacity-50">

                

                <hr />
                <div class="row">
                    <div class="col-4">

                        <div style="width: 100%; text-align: center;"><b>Правило цены</b></div>
                        @php 
                            //dd($product_rules_select);
                        @endphp

                        @if($product_rules_select)
                        <form wire:submit="searchProductForm">
                            
                                @foreach($product_rules_select as $p_rules_key => $p_rules_value)
                                <div class="mt-2 small">

                                    @if($p_rules_value['condition_field'] === 'select')
                                        <label class="form-label" for="p_rules_value{{$p_rules_key}}">{{ $p_rules_value['name'] }}</label>
                                        @php
                                            $condition_value = explode(',', $p_rules_value['condition_value']);
                                        @endphp
                                        <select
                                            class="form-select form-select-sm mb-2"
                                            wire:key="p_rules_value{{$p_rules_key}}"
                                            wire:model.defer="getRules.{{$p_rules_value['key']}}"
                                            id="p_rules_value{{$p_rules_key}}"
                                            wire:change.debounce.500ms="save()"
                                        >
                                            <option value="">---</option>
                                            @foreach($condition_value as $option)
                                                <option value="{{ $option }}">{{ $option }}</option>
                                            @endforeach
                                        </select>
                                    @elseif($p_rules_value['condition_field'] === 'checkbox')
                                        
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="p_rules_value{{$p_rules_key}}"
                                            wire:model.defer="getRules.{{$p_rules_value['key']}}"
                                            wire:key="p_rules_value{{$p_rules_key}}"
                                            wire:change.debounce.500ms="save()"
                                        />
                                        <label class="form-label" for="p_rules_value{{$p_rules_key}}">{{ $p_rules_value['name'] }}</label>
                                    @endif

                                </div>
                                @endforeach
                            
                        </form>
                        @else
                            <p>Нет правил для выбора</p>
                        @endif
                    </div>

                    <div class="col-4">
                        <div style="width: 100%; text-align: center;"><b>Общая информация</b></div>

                        <div class="mb-3">
                            <label for="modal-input1" class="form-label">Название</label>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input1" class="form-control" placeholder="Название или тип">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="modal-input2" class="form-label">Дополнительно</label>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input2" class="form-control" placeholder="Дополнительно">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="getData.manufacturer_id" class="form-label">Завод изготовитель</label>
                            <select
                                wire:key="getData.manufacturer_id"
                                wire:model.defer="getData.manufacturer_id"
                                id="getData.manufacturer_id"
                                class="form-select"
                                wire:change.debounce.500ms="updateValueManufacturer($event.target.value)"
                            >
                                @foreach($product_manufacturer_select as $manufacturer)
                                    <option value="{{ $manufacturer['id'] }}">{{ $manufacturer['name'] }}</option>
                                @endforeach
                            </select>
                        </div>



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
                            >
                                <option value="">---</option>
                                <option value="Асинхронный">Асинхронный</option>
                                <option value="Синхронный">Синхронный</option>
                            </select>
                        </div>
                    
                        <div class="mb-3">
                            <label for="getData.output_voltage" class="form-label">Номинальное напряжение,В</label>
                            <select
                                wire:key="getData.output_voltage"
                                wire:model.defer="getData.output_voltage"
                                id="getData.output_voltage"
                                class="form-select"
                                wire:change.debounce.500ms="updateValueVoltage($event.target.value)"
                            >
                                <option value="">---</option>
                                <option value="3000">3000</option>
                                <option value="3300">3300</option>
                                <option value="6000">6000</option>
                                <option value="6600">6600</option>
                                <option value="10000">10000</option>
                                <option value="11000">11000</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="getData.full_power" class="form-label">Мощность,кВт</label>
                            <input
                                type="number"
                                wire:key="getData.full_power"
                                wire:model.defer="getData.full_power"
                                id="getData.full_power"
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

                            
                    </div>
                </div>
                <hr />

                

            </div>
            



            <div class="modal-body" wire:loading>
                Загрузка фильтра ...
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger me-auto" onclick="deleteModalTarget()" data-bs-dismiss="modal" wire:loading.attr="disabled">Удалить</button>
                <button type="submit" class="btn btn-primary" onclick="saveModal()" data-bs-dismiss="modal" wire:loading.attr="disabled">Сохранить</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>
