<div class="modal fade"  wire:ignore.self id="manufacturerModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="manufacturerModalForm" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="manufacturerModalFormLabel">Изменить производителя</h1>
            </div>
            <div class="modal-body">
                
                <form wire:submit="save">
                    <x-blocks.error-message />
                    <div class="mb-3">
                        <label for="name" class="form-label">Наименование</label>
                        <input type="text" wire:model="form.name" class="form-control" placeholder="Имя" id="name" />
                        <div class="text-danger">@error('form.name') {{ $message }} @enderror</div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="$dispatch('templateCreateOpenForm')">Закрыть</button>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>