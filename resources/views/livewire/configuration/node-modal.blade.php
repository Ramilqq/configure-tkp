<div>
    <form wire:submit="saveForm" >
        <x-blocks.error-message />
        <div class="mb-3">
            <label for="title" class="form-label">Заголовок <span class="text-muted small">(отображается в панели слева)</span></label>
            <input type="text" wire:model="form.title" class="form-control" placeholder="Заголовок" id="title" />
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Название <span class="text-muted small">(отображается на схеме и в поиске)</span></label>
            <input type="text" wire:model="form.name" class="form-control" placeholder="Название" id="name" />
        </div>

        <div class="mb-3">
            <label for="extra" class="form-label">Дополнительно <span class="text-muted small">(отображается на схеме под названием)</span></label>
            <input type="text" wire:model="form.extra" class="form-control" placeholder="Дополнительно" id="extra" />
        </div>

        <div class="mb-3">
            <label for="template_id" class="form-label">Шаблон</label>
            <select class="form-select" wire:model="form.template_id" id="template_id">
                @forelse($templates as $t_key => $t_value)
                <option
                    wire:key="template_id_{{$t_key}}"
                    value="{{$t_value['id']}}"
                >
                    {{ $t_value['name'] }}
                </option>
                @empty
                    <option>Ошибка компонента!</option>
                @endforelse
            </select>
        </div>

        <div class="mb-3">
            <label for="image_upload" class="form-label">Изображение</label>
            <br />
            <span class="form-text">Максимальная высота 120px</span>
            <input type="file" wire:model="form.image_upload" class="form-control" placeholder="Изображение" id="image_upload" />
        </div>

        {{-- общий предпросмотр: изображение элемента, точки подключения и подписи на схеме;
             sticky — предпросмотр остаётся на экране при прокрутке длинной формы --}}
        <div class="my-3 mx-auto border rounded bg-white shadow-sm"
            style="width: 160px; position: sticky; top: 10px; z-index: 10;">
            @if($form->image)
                <img src="{{ $form->image }}" alt="" style="width:100%;height:100%;object-fit:contain;" />
            @else
                <div class="d-flex align-items-center justify-content-center h-100 text-muted small">Нет изображения</div>
            @endif

            {{-- точки подключения (anchor_x/anchor_y в долях 0–1, как в jsPlumb) --}}
            @foreach($form->endpoints_arr as $ep_key => $ep)
                <span wire:key="endpoint_preview_{{$ep_key}}"
                    class="position-absolute rounded-circle d-flex align-items-center justify-content-center"
                    style="left: {{ (float)($ep['anchor']['anchor_x'] ?? 0) * 100 }}%; top: {{ (float)($ep['anchor']['anchor_y'] ?? 0) * 100 }}%; width: 14px; height: 14px; background: #0d6efd; color: #fff; font-size: 9px; line-height: 1; transform: translate(-50%, -50%); pointer-events: none;"
                    title="Точка подключения №{{ (int)$ep_key + 1 }}">{{ (int)$ep_key + 1 }}</span>
            @endforeach

            {{-- подписи на схеме; привязка по левому краю, как на схеме: точка X — начало текста --}}
            @foreach($form->label_fields as $lf_key => $lf)
                @php
                    // для поля с вариантами типа в предпросмотре берётся шаблон первого варианта
                    $preview_format = !empty($lf['options'])
                        ? ($lf['options'][0]['format'] ?? '{value}')
                        : (($lf['format'] ?? '') !== '' ? $lf['format'] : '{value}');
                @endphp
                <span wire:key="label_field_preview_{{$lf_key}}"
                    class="position-absolute badge text-bg-secondary"
                    style="left: {{ (float)($lf['x'] ?? 50) }}%; top: {{ (float)($lf['y'] ?? 0) }}%; transform: translate(0, -50%); pointer-events: none;">
                    {{ str_replace('{value}', '1', $preview_format !== '' ? $preview_format : '{value}') }}
                </span>
            @endforeach
        </div>

        <div>
            @forelse($form->endpoints_arr as $endpoint_key => $endpoint_value)
                <div class="row mb-3">
                    <label for="endpoints" class="form-label">Положение точеки подключения <b>№{{(int)$endpoint_key + 1}}</b></label>
                    <div class="col">
                        <span class="form-text">Положение по Y</span>
                        <select class="form-select" wire:model.live="form.endpoints_arr.{{$endpoint_key}}.anchor.anchor_y" id="endpoints_arr_{{$endpoint_key}}_anchor_anchor_y">
                            
                            @forelse($form->anchor_y as $y_key => $y_value)
                            <option 
                                wire:key="endpoints_arr_{{$endpoint_key}}_anchor_anchor_y_{{$y_key}}" 
                                value="{{$y_value}}"
                                @if(isset($form->endpoints_arr[$endpoint_key]['anchor']['anchor_y']))
                                    @if ($form->endpoints_arr[$endpoint_key]['anchor']['anchor_y'] == $y_value) selected @endif
                                @endif
                            >
                                {{ $y_key }}
                            </option>
                            @empty
                                <option>Ошибка компонента!</option>
                            @endforelse
                        </select>
                    </div>

                    <div class="col">
                        <span class="form-text">Положение по X от 0 до 1</span>
                        <div class="d-flex align-items-center gap-1">
                            <button type="button" class="btn btn-outline-secondary btn-sm px-1 py-0"
                                wire:click="nudgeAnchorX({{$endpoint_key}}, -0.01)" title="Влево на 0.01">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <input type="range" class="form-range" min="0" max="1" step="0.01"
                                wire:model.live="form.endpoints_arr.{{$endpoint_key}}.anchor.anchor_x"
                                id="endpoints_arr_anchor_{{$endpoint_key}}_anchor_x"
                            />
                            <button type="button" class="btn btn-outline-secondary btn-sm px-1 py-0"
                                wire:click="nudgeAnchorX({{$endpoint_key}}, 0.01)" title="Вправо на 0.01">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-2">
                        <button wire:click="dllAnchor({{$endpoint_key}})" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                    </div>
                </div>
            @empty
                <b>Добавьте точку подключения!</b>
            @endforelse

            <button wire:click="addAnchor()" type="button" class="btn btn-success w-100"><i class="bi bi-plus-circle"></i></button>
        </div>

        <hr />

        <div class="mb-3">
            <label class="form-label fw-bold">Подписи на схеме</label>
            <br />
            <span class="form-text">
                Каждое поле — отдельная подпись возле элемента. «Шаблон» задаёт текст подписи,
                <code>{value}</code> заменяется введённым значением (например, шаблон <code>QS{value}</code>
                и значение <code>2</code> дадут <code>QS2</code>).
                Если у поля добавлены «варианты типа», пользователь дополнительно выбирает тип,
                и шаблон берётся из выбранного варианта (выключатель → <code>Q{value}</code> → <code>Q2</code>,
                контактор → <code>КМ{value}</code> → <code>КМ2</code>).
                Если поля не заданы, на схеме отображаются «Название» и «Дополнительно», как раньше.
            </span>

            @foreach($form->label_fields as $lf_key => $lf)
                <div class="border rounded p-2 mb-2" wire:key="label_field_{{$lf_key}}">
                    <div class="row align-items-end">
                        <div class="col">
                            <span class="form-text">Поле формы</span>
                            <input type="text" class="form-control"
                                wire:model.live.debounce.300ms="form.label_fields.{{$lf_key}}.title"
                                placeholder="Например: QS" />
                        </div>
                        @if(empty($lf['options']))
                            <div class="col">
                                <span class="form-text">Шаблон подписи</span>
                                <input type="text" class="form-control"
                                    wire:model.live.debounce.300ms="form.label_fields.{{$lf_key}}.format"
                                    placeholder="QS{value}" />
                            </div>
                        @endif
                        <div class="col">
                            <span class="form-text">X: {{ (float)($lf['x'] ?? 50) }}%</span>
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-1 py-0"
                                    wire:click="nudgeLabelField({{$lf_key}}, 'x', -1)" title="Влево на 1%">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <input type="range" class="form-range" min="-50" max="150" step="1"
                                    wire:model.live="form.label_fields.{{$lf_key}}.x" />
                                <button type="button" class="btn btn-outline-secondary btn-sm px-1 py-0"
                                    wire:click="nudgeLabelField({{$lf_key}}, 'x', 1)" title="Вправо на 1%">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col">
                            <span class="form-text">Y: {{ (float)($lf['y'] ?? 0) }}%</span>
                            <div class="d-flex align-items-center gap-1">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-1 py-0"
                                    wire:click="nudgeLabelField({{$lf_key}}, 'y', -1)" title="Вверх на 1%">
                                    <i class="bi bi-chevron-left"></i>
                                </button>
                                <input type="range" class="form-range" min="-50" max="150" step="1"
                                    wire:model.live="form.label_fields.{{$lf_key}}.y" />
                                <button type="button" class="btn btn-outline-secondary btn-sm px-1 py-0"
                                    wire:click="nudgeLabelField({{$lf_key}}, 'y', 1)" title="Вниз на 1%">
                                    <i class="bi bi-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-2">
                            <button wire:click="dllLabelField({{$lf_key}})" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>

                    {{-- варианты типа: селект в форме, у каждого типа свой шаблон подписи --}}
                    <div class="ms-4 mt-2 border-start ps-3">
                        @foreach(($lf['options'] ?? []) as $opt_key => $opt)
                            <div class="row mb-1 align-items-end" wire:key="label_field_{{$lf_key}}_option_{{$opt_key}}">
                                <div class="col">
                                    <span class="form-text">Тип</span>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.live.debounce.300ms="form.label_fields.{{$lf_key}}.options.{{$opt_key}}.title"
                                        placeholder="Например: Контактор" />
                                </div>
                                <div class="col">
                                    <span class="form-text">Шаблон подписи</span>
                                    <input type="text" class="form-control form-control-sm"
                                        wire:model.live.debounce.300ms="form.label_fields.{{$lf_key}}.options.{{$opt_key}}.format"
                                        placeholder="КМ{value}" />
                                </div>
                                <div class="col-2">
                                    <button wire:click="dllLabelFieldOption({{$lf_key}}, {{$opt_key}})" type="button" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        @endforeach

                        <button wire:click="addLabelFieldOption({{$lf_key}})" type="button" class="btn btn-outline-secondary btn-sm mt-1">
                            <i class="bi bi-plus-circle me-1"></i>Добавить вариант типа
                        </button>
                    </div>
                </div>
            @endforeach

            <button wire:click="addLabelField()" type="button" class="btn btn-outline-success w-100">
                <i class="bi bi-plus-circle me-1"></i>Добавить подпись
            </button>
        </div>

        <div class="modal-footer">
            <x-blocks.button-close />
            <x-blocks.button-submit />
        </div>
    </form>
</div>
