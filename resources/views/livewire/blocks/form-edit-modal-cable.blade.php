<div class="modal fade" id="editModalCable" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" wire:ignore>
                <h5 class="modal-title" id="modal-title-conn"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">


                <div class="row g-3 align-items-center pb-1">
                    <div class="col-auto">
                        Название для схеме
                    </div>
                    <div class="col-auto" style="margin-left:auto;">
                        <input type="text" id="modal-input10" class="form-control mb-2" placeholder="Кабель"
                            wire:model.defer="getData.name"
                            wire:change.debounce.500ms="updateData($event.target.value)"
                        >
                    </div>
                </div>
                
                <div class="row g-3 align-items-center pb-1">
                    <div class="col-auto">
                        Длинна для схеме
                    </div>
                    <div class="col-auto" style="margin-left:auto;">
                        <input type="text" id="modal-input11" class="form-control mb-2" placeholder="1 метр"
                            wire:model.defer="getData.length"
                            wire:change.debounce.500ms="updateData($event.target.value)"
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label for="getData.supplier.cable" class="form-label">Поставщик</label>
                    <select
                        wire:key="getData.supplier.cable"
                        wire:model.defer="getData.supplier"
                        id="getData.supplier.cable"
                        class="form-select"
                        wire:change.debounce.500ms="updateData($event.target.value)"
                    >
                    <?php 
                        $selected = '';
                    ?>
                        @foreach($product_manufacturer_select as $manufacturer)
                            <?php 
                                if($manufacturer['name'] == $getData['manufacturer']) {
                                    $selected = 'selected';
                                }
                            ?>
                            <option value="{{ $manufacturer['name'] }}" {{$selected}}>{{ $manufacturer['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="getData.manufacturer" class="form-label">Производитель</label>
                    <input
                        type="text"
                        wire:key="getData.manufacturer"
                        wire:model.defer="getData.manufacturer"
                        id="getData.manufacturer"
                        class="form-control"
                        placeholder="Производитель" 
                        wire:change.debounce.500ms="updateData($event.target.value)"
                    >
                </div>

                <div class="mb-3">
                    <label for="getData.price" class="form-label">Цена общая. RUB</label>
                    <input
                        type="number"
                        wire:key="getData.price"
                        wire:model.defer="getData.price"
                        id="getData.price"
                        class="form-control"
                        placeholder="0" 
                        wire:change.debounce.500ms="updateData($event.target.value)"
                        step="0.1"
                    >
                </div>

                <hr />
                <div style="width: 100%; text-align: center;">Фильтр добавления продукта</div>

                <form wire:submit="searchProductForm">

                    @forelse($product_filter_select as $p_filter_key => $p_filter_value)

                    <div class="row g-3 align-items-center pb-1">
                        <div class="col-auto">
                            <label for="inputPassword6" class="col-form-label">{{$p_filter_value['name']}}</label>
                        </div>
                        <div class="col-auto" style="margin-left:auto;">
                        
                            <select class="form-select" id="product_filter_{{$p_filter_key}}"
                                name="{{$p_filter_key}}"
                                wire:model="getData.{{$p_filter_value['key']}}"
                            >
                                <option value="" selected>---</option>
                                @forelse($p_filter_value['fields'] as $fields_key => $fields_val)
                                    <option value="{{$fields_val}}" wire:key="product_filter_field_{{$fields_key}}">{{$fields_val}}</option>
                                @empty
                                    <option value="">Нет данных</option>
                                @endforelse
                            </select>

                        </div>
                    </div>
                    @empty
                        <p>Нет фильтров для выбора</p>
                    @endforelse
                </form>


                <hr />
                <div style="width: 100%; text-align: center;">Правило цены</div>
                @if($product_rules_select)
                <form wire:submit="searchProductForm">
                    <div class="mt-2 small">
                        @foreach($product_rules_select as $p_rules_key => $p_rules_value)
                            <label class="form-check">
                                <input class="form-check-input"
                                    type="checkbox"
                                    wire:model="getRules.{{$p_rules_value['key']}}"
                                >
                                <span>{{ $p_rules_value['name'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </form>
                @else
                    <p>Нет правил для выбора</p>
                @endif

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
                <button type="button" class="btn btn-danger me-auto" onclick="deleteModalTarget()" data-bs-dismiss="modal">Удалить</button>
                <button type="button" class="btn btn-primary" onclick="saveModal()">Сохранить</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
            </div>
        </div>
    </div>
</div>