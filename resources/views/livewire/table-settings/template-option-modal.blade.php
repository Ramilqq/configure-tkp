<div>
    <form wire:submit="saveForm">
        <x-blocks.error-message />
        <div class="mb-3">
            <label for="name" class="form-label">Название</label>
            <input type="text" wire:model="form.name" class="form-control" placeholder="Название" id="name" />
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="$dispatch('templateCreateOpenForm')">Закрыть</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>
