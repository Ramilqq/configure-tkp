<div>
    <form wire:submit="saveForm">
        <x-blocks.error-message />
        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" wire:model="form.name" class="form-control" placeholder="Имя" id="name" />
        </div>
        
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

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="$dispatch('nodeGroupCreateOpenForm')">Закрыть</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>
