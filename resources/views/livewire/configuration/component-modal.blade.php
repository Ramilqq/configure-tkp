<div>
    <form wire:submit="saveForm" >
        <x-blocks.error-message />
        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" wire:model="form.name" class="form-control" placeholder="Имя" id="name" />
        </div>

        <div class="mb-3">
            <label for="image_upload" class="form-label">Изображение</label>
            <br />
            <span class="form-text">Максимальная высота 120px</span>
            <input type="file" wire:model="form.image_upload" class="form-control" placeholder="Изображение" id="image_upload" />
        </div>

        <div>
            @forelse($form->endpoints_arr as $endpoint_key => $endpoint_value)
                <div class="row mb-3">
                    <label for="endpoints" class="form-label">Положение точеки подключения <b>№{{(int)$endpoint_key + 1}}</b></label>
                    <div class="col">
                        <span class="form-text">Положение по Y</span>
                        <select class="form-select" wire:model="form.endpoints_arr.{{$endpoint_key}}.anchor.anchor_y" id="endpoints_arr_{{$endpoint_key}}_anchor_anchor_y">
                            
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
                        <input type="range" class="form-range" min="0" max="1" step="0.05" 
                            wire:model="form.endpoints_arr.{{$endpoint_key}}.anchor.anchor_x"
                            id="endpoints_arr_anchor_{{$endpoint_key}}_anchor_x" 
                        />
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

        <div class="modal-footer">
            <x-blocks.button-close />
            <x-blocks.button-submit />
        </div>
    </form>
</div>
