<div>
    <form wire:submit.prevent="saveForm">
        <x-blocks.error-message />

        {{-- Основные поля --}}
        <div class="mb-3">
            <label class="form-label">Название правила</label>
            <input type="text" class="form-control" wire:model.lazy="form.name" placeholder="Напр. Надбавка за синхронный двигатель" />
            <div class="text-danger">@error('form.name') {{ $message }} @enderror</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Описание</label>
            <input type="text" class="form-control" wire:model.lazy="form.description" placeholder="Краткое описание" />
            <div class="text-danger">@error('form.description') {{ $message }} @enderror</div>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label">Включено</label>
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" wire:model="form.enabled" id="ruleEnabled">
                    <label class="form-check-label" for="ruleEnabled">Активно</label>
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
                    <option value="add">Прибавить (base + value)</option>
                    <option value="replace">Заменить (= value)</option>
                    <option value="multiply">Умножить (base × value)</option>
                </select>
                <div class="text-danger">@error('form.mode') {{ $message }} @enderror</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label">Значение</label>
                <input type="number" step="any" class="form-control" wire:model.lazy="form.value" placeholder="120000" />
                <div class="text-danger">@error('form.value') {{ $message }} @enderror</div>
            </div>
            <div class="col-6">
                <label class="form-label">Валюта</label>
                <select class="form-select" wire:model="form.currency">
                    @foreach($currencies as $cur)
                        <option value="{{ $cur }}">{{ $cur }}</option>
                    @endforeach
                </select>
                <div class="text-danger">@error('form.currency') {{ $message }} @enderror</div>
            </div>
        </div>

        <hr />

        {{-- Условия по опциям продукта (ProductOption.value) --}}
        <h6>Условия по опциям продукта</h6>
        <div class="form-text mb-2">Проверяем значение опции продукта (ProductOption)</div>

        @foreach($form->option_conditions as $i => $cond)
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-4">
                    <select class="form-select form-select-sm" wire:model.number="form.option_conditions.{{ $i }}.template_option_id">
                        <option value="">— опция —</option>
                        @foreach($options as $opt)
                            <option value="{{ $opt['id'] }}">{{ $opt['name'] }} ({{ $opt['key'] }})</option>
                        @endforeach
                    </select>
                    <div class="text-danger small">@error('form.option_conditions.' . $i . '.template_option_id') {{ $message }} @enderror</div>
                </div>
                <div class="col-2">
                    <select class="form-select form-select-sm" wire:model="form.option_conditions.{{ $i }}.operator">
                        <option value="=">=</option>
                        <option value=">">&gt;</option>
                        <option value=">=">&gt;=</option>
                        <option value="<">&lt;</option>
                        <option value="<=">&lt;=</option>
                    </select>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control form-control-sm" wire:model.lazy="form.option_conditions.{{ $i }}.value" placeholder="значение" />
                    <div class="text-danger small">@error('form.option_conditions.' . $i . '.value') {{ $message }} @enderror</div>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-danger btn-sm w-100" wire:click="removeOptionCondition({{ $i }})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach

        <button type="button" class="btn btn-outline-secondary btn-sm mb-3" wire:click="addOptionCondition">
            <i class="bi bi-plus-circle"></i> Добавить условие по опции
        </button>

        <hr />

        {{-- Условия по ценам опций продукта (ProductOptionPrice.price) --}}
        <h6>Условия по ценам опций продукта</h6>
        <div class="form-text mb-2">Проверяем цену опции продукта (ProductOptionPrice)</div>

        @foreach($form->option_price_conditions as $i => $cond)
            <div class="row g-2 mb-2 align-items-center">
                <div class="col-4">
                    <select class="form-select form-select-sm" wire:model.number="form.option_price_conditions.{{ $i }}.template_option_id">
                        <option value="">— опция —</option>
                        @foreach($options as $opt)
                            <option value="{{ $opt['id'] }}">{{ $opt['name'] }} ({{ $opt['key'] }})</option>
                        @endforeach
                    </select>
                    <div class="text-danger small">@error('form.option_price_conditions.' . $i . '.template_option_id') {{ $message }} @enderror</div>
                </div>
                <div class="col-2">
                    <select class="form-select form-select-sm" wire:model="form.option_price_conditions.{{ $i }}.operator">
                        <option value="=">=</option>
                        <option value=">">&gt;</option>
                        <option value=">=">&gt;=</option>
                        <option value="<">&lt;</option>
                        <option value="<=">&lt;=</option>
                    </select>
                </div>
                <div class="col-4">
                    <input type="number" step="any" class="form-control form-control-sm" wire:model.lazy="form.option_price_conditions.{{ $i }}.value" placeholder="цена" />
                    <div class="text-danger small">@error('form.option_price_conditions.' . $i . '.value') {{ $message }} @enderror</div>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-danger btn-sm w-100" wire:click="removeOptionPriceCondition({{ $i }})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach

        <button type="button" class="btn btn-outline-secondary btn-sm mb-3" wire:click="addOptionPriceCondition">
            <i class="bi bi-plus-circle"></i> Добавить условие по цене опции
        </button>

        <div class="modal-footer px-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>
