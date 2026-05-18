<div class="modal fade" id="editModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog" style="--bs-modal-width:800px !important">
        <div class="modal-content">
            <div class="modal-header" wire:ignore>
                <h5 class="modal-title" id="modal-title-node"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body" wire:loading.class="opacity-50">

                <div class="row">
                    <div class="col-6">

                        <div style="width: 100%; text-align: center;"><b>Опции</b></div>

                        @foreach ($product_filter_select as $key => $product_filter) 
                            <div class="mb-3" wire:key="getDataDiv-{{$key}}">
                                
                                <label for="getData-{{$key}}" class="form-label" style="font-size: 11px;">{{$product_filter['name']}}.</label>
                                <select
                                    wire:key="getData-{{$key}}"
                                    wire:model.defer="getData.{{$key}}"
                                    id="getData-{{$key}}"
                                    class="form-select"
                                    wire:change.debounce.500ms="updateData('{{$key}}', $event.target.value)"
                                    style="font-size: 11px;"
                                    
                                    @foreach ($product_filter['fields'] as $field)
                                        @if ($product_filter['key'] == 'power_cell_bypass' && $field == 'Электронный')
                                        @else
                                        <option value="{{$field}}">{{$field}}</option>
                                        @endif
                                    @endforeach
                    
                                </select>
                                
                            </div>
                        @endforeach
                        
                    </div>

                    <div class="col-6">
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
                            <label for="getData.manufacturer.other" class="form-label">Завод изготовитель</label>
                            <select
                                wire:key="getData.manufacturer.other"
                                wire:model.defer="getData.manufacturer"
                                id="getData.manufacturer.other"
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
                <button type="submit" class="btn btn-primary" onclick="saveModal()" wire:loading.attr="disabled">Сохранить</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>
