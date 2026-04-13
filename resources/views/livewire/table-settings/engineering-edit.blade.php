<div class="modal fade"  wire:ignore.self id="engineeringModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="engineeringModalFormLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="engineeringModalFormLabel">@if ($form->id) Изменить @else Создать @endif инженерные данные</h1>
            </div>
            <div class="modal-body">
                
                <form wire:submit="save">
                    <x-blocks.error-message />
                    <div class="mb-3">
                        <label for="name" class="form-label">Наименование</label>
                        <input type="text" wire:model="form.name" class="form-control" placeholder="Имя" id="name" />
                        <div class="text-danger">@error('form.name') {{ $message }} @enderror</div>
                    </div>
                    <div class="mb-3">
                        <label for="key" class="form-label">Краткое обозночение</label>
                        <input type="text" wire:model="form.key" class="form-control" placeholder="Описание" id="key" />
                        <div class="text-danger">@error('form.key') {{ $message }} @enderror</div>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Цена за час</label>
                        <input type="text" wire:model="form.price" class="form-control" placeholder="Цена за час" id="price" />
                        <div class="text-danger">@error('form.price') {{ $message }} @enderror</div>
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