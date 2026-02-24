<div>
    <form wire:submit.prevent="saveForm">
        <x-blocks.error-message />

        <div class="mb-3">
            <label class="form-label">Название схемы</label>
            <input type="text" class="form-control" wire:model.lazy="form.name" placeholder="Напр. Серия A / мощность 11-15" />
            <div class="text-danger">@error('form.name') {{ $message }} @enderror</div>
        </div>

        <div class="row mb-3">
            <div class="col-4">
                <label class="form-label">Включено</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" wire:model="form.enabled" id="enabledScheme">
                    <label class="form-check-label" for="enabledScheme">Активно</label>
                </div>
            </div>
            <div class="col-4">
                <label class="form-label">Приоритет (sort)</label>
                <input type="number" class="form-control" wire:model="form.sort" />
                <div class="text-danger">@error('form.sort') {{ $message }} @enderror</div>
            </div>
            <div class="col-4">
                <label class="form-label">Match mode</label>
                <select class="form-select" wire:model="form.match_mode">
                    <option value="all">ALL (все условия)</option>
                    <option value="any">ANY (хотя бы одно)</option>
                </select>
            </div>
        </div>

        <hr />

        <h6>Условия по опциям продукта</h6>
        <div class="row g-2 mb-2 text-muted">
            <div class="col-4">Опция (key)</div>
            <div class="col-3">Оператор</div>
            <div class="col-4">Значение</div>
            <div class="col-1"></div>
        </div>

        @php
            // какие ключи уже выбраны (кроме текущей строки)
            $usedConditionKeys = collect($form->conditions)
                ->pluck('option_key')
                ->filter()
                ->values()
                ->all();
        @endphp

        @foreach($form->conditions as $i => $row)
        @php
            // какой ключ выбран текущая строка
            $current = $row['option_key'] ?? '';
            $usedExceptCurrent = array_values(array_diff($usedConditionKeys, [$current]));
        @endphp
            <div class="row g-2 mb-2 align-items-center" wire:key="conditions-{{ $row['_k'] }}">
                <div class="col-4">
                    <select class="form-select" wire:model="form.conditions.{{ $i }}.option_key" wire:key="conditions-option_key-{{ $row['_k'] }}">
                        <option value="">— выберите опцию —</option>
                        @foreach($options as $opt)
                        @php
                            // отключаем существующее ключ
                            $disabled = in_array($opt['key'], $usedExceptCurrent, true);
                        @endphp
                            <option value="{{ $opt['key'] }}" @disabled($disabled)>{{ $opt['name'] }} ({{ $opt['key'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <select class="form-select" wire:model="form.conditions.{{ $i }}.op" wire:key="conditions-op-{{ $row['_k'] }}">
                        <option value="equals">равно</option>
                        <option value="not_equals">не равно</option>
                        <option value="exists">существует</option>
                        <option value="filled">заполненный</option>
                        <option value="in">имеет (через запятую)</option>
                        <option value="not_in">не имеет (через запятую)</option>
                        <option value="contains">содержит</option>
                    </select>
                </div>
                <div class="col-4">
                   <input type="text" class="form-control" wire:model.lazy="form.conditions.{{ $i }}.value" placeholder="A или 11,15" wire:key="conditions-value-{{ $row['_k'] }}" />
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger w-100" wire:click="removeConditionRow({{ $i }})" wire:key="removeConditionRow-{{ $row['_k'] }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach

        <button type="button" class="btn btn-success w-100 mb-3" wire:click="addConditionRow">
            <i class="bi bi-plus-circle"></i> Добавить условие по опции
        </button>

        <hr />

        <h6>Условия по правилам цены (rules_fields)</h6>
        <div class="row g-2 mb-2 text-muted">
            <div class="col-4">Правило (key)</div>
            <div class="col-3">Оператор</div>
            <div class="col-4">Значение</div>
            <div class="col-1"></div>
        </div>

        @php
            // какие ключи уже выбраны (кроме текущей строки)
            $usedRuleKeys = collect($form->rule_conditions)
                ->pluck('rule_key')
                ->filter()
                ->values()
                ->all();
        @endphp

        @foreach($form->rule_conditions as $i => $row)
        @php
            // какой ключ выбран текущая строка
            $current = $row['rule_key'] ?? '';
            $usedExceptCurrent = array_values(array_diff($usedRuleKeys, [$current]));
        @endphp
            <div class="row g-2 mb-2 align-items-center" wire:key="rule_conditions-{{ $row['_k'] }}">
                <div class="col-4">
                    <select class="form-select" wire:model="form.rule_conditions.{{ $i }}.rule_key" wire:key="rule_conditions-rule_key-{{ $row['_k'] }}">
                        <option value="">— выберите правило —</option>
                        @foreach($rules as $r)
                        @php
                            // отключаем существующее ключ
                            $disabled = in_array($r['key'], $usedExceptCurrent, true);
                        @endphp
                            <option value="{{ $r['key'] }}" @disabled($disabled)>{{ $r['name'] }} ({{ $r['key'] }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-3">
                    <select class="form-select" wire:model="form.rule_conditions.{{ $i }}.op" wire:key="rule_conditions-op-{{ $row['_k'] }}">
                        <option value="equals">равно</option>
                        <option value="not_equals">не равно</option>
                        <option value="exists">существует</option>
                        <option value="filled">заполненный</option>
                        <option value="in">имеет (через запятую)</option>
                        <option value="not_in">не имеет (через запятую)</option>
                        <option value="contains">содержит</option>
                    </select>
                </div>
                <div class="col-4">
                    <input type="text" class="form-control" wire:model.lazy="form.rule_conditions.{{ $i }}.value" placeholder="1 или on" wire:key="rule_conditions-value-{{ $row['_k'] }}" />
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-danger w-100" wire:click="removeRuleConditionRow({{ $i }})" wire:key="removeRuleConditionRow-{{ $row['_k'] }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        @endforeach

        <button type="button" class="btn btn-success w-100 mb-3" wire:click="addRuleConditionRow">
            <i class="bi bi-plus-circle"></i> Добавить условие по правилу
        </button>

        <hr />

        <h6>Картинки схемы</h6>

        @if(!empty($images))
            <div class="mb-2">
                @foreach($images as $key => $img)
                    <div class="d-flex align-items-center justify-content-between border rounded p-2 mb-2" wire:key="img-{{ $img['id'] }}">
                        <div class="row">
                            <div class="col-4 m-auto">
                                <img class="w-50" src="{{ $img['file_path'] }}" alt="">
                            </div>
                            <div class="col-8">
                                <div class="small">
                                    <label class="form-label" for="images.{{$key}}.title">Название схемы</label>
                                    <input type="hidden" id="images.{{$key}}.id" wire:model="formImage.images.{{$key}}.id">
                                    <input type="text" class="form-control" id="images.{{$key}}.title" wire:model="formImage.images.{{$key}}.title">
                                    <label class="form-label" for="images.{{$key}}.title">Приоритет</label>
                                    <input type="text" class="form-control" id="images.{{$key}}.sort" wire:model="formImage.images.{{$key}}.sort">
                                    <div class="text-muted">{{ $img['file_path'] }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <button type="button" class="btn btn-danger btn-sm"
                                @click="$dispatch('dimensionSchemeRemoveImage', {image_id : {{ $img['id'] }} })"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label">Добавить новые картинки (jpg/png/webp)</label>
            <input type="file" class="form-control" wire:model="newImages" multiple>
            <div class="text-danger">@error('newImages.*') {{ $message }} @enderror</div>
            <div class="form-text">Файлы сохраняются в public/assets/image/dimensions/&lt;template&gt;/&lt;scheme&gt;/</div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>