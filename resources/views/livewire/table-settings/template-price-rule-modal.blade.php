<div>
    <form wire:submit.prevent="saveForm">
        <x-blocks.error-message />

        <div class="mb-3">
            <label class="form-label">Название правила</label>
            <input type="text" class="form-control" wire:model.lazy="form.name" placeholder="Напр. Синхронизация по мощности" />
            <div class="text-danger">@error('form.name') {{ $message }} @enderror</div>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label">Включено</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model="form.enabled" id="enabledRule">
                    <label class="form-check-label" for="enabledRule">Активно</label>
                </div>
            </div>
            <div class="col-6">
                <label class="form-label">Приоритет (sort)</label>
                <input type="number" class="form-control" wire:model="form.sort" />
                <div class="text-danger">@error('form.sort') {{ $message }} @enderror</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label">Что меняем</label>
                <select class="form-select" wire:model="form.target_field">
                    <option value="price">Цена оборудования (price)</option>
                    <option value="delivery">Цена доставки (delivery)</option>
                </select>
                <div class="text-danger">@error('form.target_field') {{ $message }} @enderror</div>
            </div>
            <div class="col-6">
                <label class="form-label" for="form.mode">Режим</label>
                <select class="form-select" wire:model="form.mode" id="form.mode">
                    <option value="add">Прибавить (target = base + value)</option>
                    <option value="replace">Заменить (target = value)</option>
                    <option value="multiply">Умножить (target = base * value)</option>
                </select>
                <div class="text-danger">@error('form.mode') {{ $message }} @enderror</div>
            </div>
        </div>

        <hr />

        <h6>Генерация названия продукта</h6>

        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label">Включено</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model="form.generation_name_status" id="generationNameStatus">
                    <label class="form-check-label" for="generationNameStatus">Активно</label>
                </div>
            </div>
            <div class="col-6" wire:show="form.generation_name_status">
                <label class="form-label">Текст для генерации названия</label>
                <input type="text" class="form-control" wire:model.lazy="form.generation_name_text" placeholder="Напр. для синхронизация: S" />
                <div class="text-danger">@error('form.generation_name_text') {{ $message }} @enderror</div>
                <div class="form-text">Если пусто, то будет брать выбранное значение из "Условие срабатывания правила"</div>
            </div>
        </div>

        <div class="form-text">Если включено, то при срабатывании правила будет генерироваться название продукта по заданному шаблону</div>
        

        <hr />

        <h6>Условие срабатывания правила</h6>
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label" for="form.condition_operator">Оператор</label>
                <select class="form-select" wire:model="form.condition_operator" id="form.condition_operator">
                    <option value="equals">Равно</option>
                    <option value="exists">Есть значение драйвера (опция существует)</option>
                    <option value="filled">Заполнено (value не пусто)</option>
                    <option value="not_equals">Не равно</option>
                </select>
                <div class="text-danger">@error('form.condition_operator') {{ $message }} @enderror</div>
            </div>

            <div class="col-6">
                <label class="form-label" wire:show="form.condition_field == 'input'">Значение (Строка)</label>
                <label class="form-label" wire:show="form.condition_field == 'select'">Значение (Строка через запятую)</label>
                <label class="form-label" wire:show="form.condition_field == 'checkbox'">Значение (1 или 0)</label>
                <input type="text" class="form-control" wire:model.lazy="form.condition_value" placeholder="Значение для оператора" />
                <div class="text-danger">@error('form.condition_value') {{ $message }} @enderror</div>
            </div>
            
            <div class="col-6">
                <label class="form-label" for="form.condition_field">Тип правила</label>

                <select class="form-select" wire:model="form.condition_field" id="form.condition_field">
                    <option value="input">Строка</option>
                    <option value="select">Выпадающий список</option>
                    <option value="checkbox">Чекбокс</option>
                </select>
                <div class="text-danger">@error('form.condition_field') {{ $message }} @enderror</div>
            </div>

        </div>

        <hr />

        <h6>Доп. условие по опции продукта (текст, без цены) — опционально</h6>

        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label" for="text_option_id">Опция-продукта</label>
                <div wire:key="text-select-{{ $form->template_id }}-{{ $form->id }}-{{ count($options) }}">
                    <select class="form-select" wire:model.number="form.text_option_id" id="text_option_id">
                        <option value="">— не выбрано —</option>
                        @foreach($options as $opt)
                            <option value="{{ $opt['id'] }}">{{ $opt['name'] }} ({{ $opt['key'] }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="text-danger">@error('form.text_option_id') {{ $message }} @enderror</div>
                
            </div>

            <div class="col-6" wire:show="form.text_option_id">
                <label class="form-label">Оператор</label>
                <select class="form-select" wire:model="form.text_operator">
                    <option value="exists">Есть значение</option>
                    <option value="filled">Заполнено</option>
                    <option value="equals">Равно</option>
                    <option value="not_equals">Не равно</option>
                </select>
                <div class="text-danger">@error('form.text_operator') {{ $message }} @enderror</div>
            </div>

            <div class="col-6" wire:show="form.text_option_id">
                <label class="form-label" for="form.text_field">Тип текстового поля</label>
                <select class="form-select" wire:model="form.text_field" id="form.text_field">
                    <option value="input">Строка</option>
                    <option value="select">Выпадающий список</option>
                    <option value="checkbox">Чекбокс</option>
                </select>
                <div class="text-danger">@error('form.text_field') {{ $message }} @enderror</div>
            </div>

            <div class="col-6" wire:show="form.text_option_id">
                <label class="form-label" wire:show="form.text_field == 'input'">Значение (Строка)</label>
                <label class="form-label" wire:show="form.text_field == 'select'">Значение (Строка через запятую)</label>
                <label class="form-label" wire:show="form.text_field == 'checkbox'">Значение (1 или 0)</label>

                <input type="text" class="form-control" wire:model.lazy="form.text_value" placeholder="Значение для оператора" />
                <div class="text-danger">@error('form.text_value') {{ $message }} @enderror</div>
            </div>

            

            <div class="form-text">
                Для дополнительных условий привязки "Правила цены" продукта.
            </div>
        </div>

        <hr />

        <h6>Условие по опции продукта (диапазон значений)</h6>
        <div class="mb-3">
            <label class="form-label" for="driver_option_id">Опция-продукта</label>

            <div wire:key="driver-select-{{ $form->template_id }}-{{ $form->id }}-{{ count($options) }}">
                <select class="form-select" wire:model.number="form.driver_option_id" id="driver_option_id">
                    <option value="">— не выбрано —</option>
                    @foreach($options as $opt)
                        <option value="{{ $opt['id'] }}">{{ $opt['name'] }} ({{ $opt['key'] }})</option>
                    @endforeach
                </select>
            </div>
            <div class="text-danger">@error('form.driver_option_id') {{ $message }} @enderror</div>
            <div class="form-text">
                Значение берём из "Опции продукта" по выбранной опции.
            </div>
        </div>

        <div wire:show="form.driver_option_id">
            <h6>Таблица (диапазоны)</h6>

            <div class="row g-2 mb-2 text-muted">
                <div class="col-2">from (>=)</div>
                <div class="col-2">to (<)</div>
                <div class="col-2">Условие</div>
                <div class="col-2"><span wire:show="form.text_option_id">Доп. опция</span></div>
                <div class="col-2">Стоимость</div>
                <div class="col-2"></div>
            </div>

            @php
                $condition_field = $form->condition_field ?? null;

                if ($condition_field === 'select') {
                    $condition_value = explode(',', $form->condition_value) ?: [];
                } elseif ($condition_field === 'checkbox') {
                    $condition_value = $form->condition_value ? '1' : '0';
                } else {
                    $condition_value = $form->condition_value;
                }

            @endphp
            
            @php
                $text_field = $form->text_field ?? null;

                if ($text_field === 'select') {
                    $text_value = explode(',', $form->text_value) ?: [];
                } elseif ($text_field === 'checkbox') {
                    $text_value = $form->text_value ? '1' : '0';
                } else {
                    $text_value = $form->text_value;
                }

            @endphp

            @foreach($form->mapping as $i => $row)
                <div class="row g-2 mb-2 align-items-center">
                    <div class="col-2">
                        <input type="text" class="form-control" wire:model="form.mapping.{{ $i }}.from" placeholder="0" />
                    </div>
                    <div class="col-2">
                        <input type="text" class="form-control" wire:model="form.mapping.{{ $i }}.to" placeholder="100" />
                    </div>

                    <div class="col-2">
                        <select class="form-select" wire:model="form.mapping.{{ $i }}.condition">

                            @if($condition_field === 'select')
                                <option value="">---</option>
                                @foreach($condition_value as $val)
                                    <option value="{{ $val }}">{{ $val }}</option>
                                @endforeach
                            @elseif($condition_field === 'checkbox')
                                <option value="{{$condition_value}}">{{ $condition_value === '1' ? 'Да' : 'Нет' }}</option>
                            @else
                                <option value="{{$condition_value}}">{{ $condition_value }}</option>
                            @endif

                        </select>
                    </div>

                    <div class="col-2">
                        <select class="form-select" wire:model="form.mapping.{{ $i }}.text" wire:show="form.text_option_id">

                            @if($text_field === 'select')
                                    <option value="">---</option>
                                @foreach($text_value as $val)
                                    <option value="{{ $val }}">{{ $val }}</option>
                                @endforeach
                            @elseif($text_field === 'checkbox')
                                <option value="{{$text_value}}">{{ $text_value === '1' ? 'Да' : 'Нет' }}</option>
                            @else
                                <option value="{{$text_value}}">{{ $text_value }}</option>
                            @endif
                            
                        </select>
                    </div>
                    

                    <div class="col-2">
                        <input type="text" class="form-control" wire:model="form.mapping.{{ $i }}.value" placeholder="120000 или 1.15" />
                    </div>
                    <div class="col-2">
                        <button type="button" class="btn btn-danger w-100" wire:click="removeMappingRow({{ $i }})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            @endforeach
            <button type="button" class="btn btn-success w-100 mb-3" wire:click="addMappingRow">
                <i class="bi bi-plus-circle"></i> Добавить строку
            </button>
        </div>

        <div wire:show="!form.driver_option_id">
            <div class="mb-3">
                <label class="form-label">Фикс-значение (если условие не выбрано)</label>
                <input type="text" class="form-control" wire:model.lazy="form.fixed_value" placeholder="120000 или 1.15">
                <div class="text-danger">@error('form.fixed_value') {{ $message }} @enderror</div>
                <div class="form-text">
                    Используется для изменения цены без условия или только по доп. условию.
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>
