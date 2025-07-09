<div>
    <form wire:submit="saveForm">
        
        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" wire:model="form.name" class="form-control" placeholder="Имя" id="name" />
            <div class="text-danger">@error('form.name') {{ $message }} @enderror</div>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Описание</label>
            <input type="text" wire:model="form.description" class="form-control" placeholder="Описание" id="description" />
            <div class="text-danger">@error('form.description') {{ $message }} @enderror</div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="$dispatch('templateCreateOpenForm')">Закрыть</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>
