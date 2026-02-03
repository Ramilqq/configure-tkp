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
                <label class="form-label">Режим</label>
                <select class="form-select" wire:model="form.mode">
                    <option value="replace">Заменить (target = value)</option>
                    <option value="add">Прибавить (target = base + value)</option>
                    <option value="multiply">Умножить (target = base * value)</option>
                </select>
                <div class="text-danger">@error('form.mode') {{ $message }} @enderror</div>
            </div>
        </div>

        <hr />

        <h6>Условие срабатывания (по значению драйвера)</h6>
        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label">Оператор</label>
                <select class="form-select" wire:model="form.condition_operator">
                    <option value="exists">Есть значение драйвера (опция существует)</option>
                    <option value="filled">Заполнено (value не пусто)</option>
                    <option value="equals">Равно</option>
                    <option value="not_equals">Не равно</option>
                </select>
                <div class="text-danger">@error('form.condition_operator') {{ $message }} @enderror</div>
            </div>

            <div class="col-6">
                <label class="form-label">Значение для equals / not_equals</label>
                <input type="text" class="form-control" wire:model.lazy="form.condition_value" placeholder="Напр. Да / 1 / 63" />
                <div class="text-danger">@error('form.condition_value') {{ $message }} @enderror</div>
            </div>
        </div>

        <hr />

        <h6>Драйвер (по какой опции делать lookup)</h6>
        <div class="mb-3">
            <label class="form-label" for="driver_option_id">Опция-драйвер</label>

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
                Значение берём из ProductOption.value по выбранной опции-драйверу.
            </div>
        </div>

        <h6>Таблица mapping (диапазоны)</h6>

        <div class="row g-2 mb-2 text-muted">
            <div class="col-3">from</div>
            <div class="col-3">to</div>
            <div class="col-4">value</div>
            <div class="col-2"></div>
        </div>

        @foreach($form->mapping as $i => $row)
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-3">
                    <input type="text" class="form-control" wire:model="form.mapping.{{ $i }}.from" placeholder="0" />
                </div>
                <div class="col-3">
                    <input type="text" class="form-control" wire:model="form.mapping.{{ $i }}.to" placeholder="100" />
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" wire:model="form.mapping.{{ $i }}.value" placeholder="120000 / 1.15" />
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

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>
