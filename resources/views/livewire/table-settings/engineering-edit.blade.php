<div class="modal fade" wire:ignore.self id="engineeringModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="engineeringModalFormLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header modal-header-brand">
                <h5 class="modal-title text-white" id="engineeringModalFormLabel">
                    <i class="bi bi-cpu me-2"></i>@if ($form->id) Изменить @else Создать @endif инженерные данные
                </h5>
            </div>
            <div class="modal-body">
                <form wire:submit="save">
                    <x-blocks.error-message />

                    <div class="mb-3">
                        <label for="eng_name" class="form-label small fw-semibold">Наименование</label>
                        <input type="text" wire:model="form.name" class="form-control" placeholder="Наименование" id="eng_name" />
                        <div class="text-danger small">@error('form.name') {{ $message }} @enderror</div>
                    </div>
                    <div class="mb-3">
                        <label for="eng_key" class="form-label small fw-semibold">Краткое обозначение</label>
                        <input type="text" wire:model="form.key" class="form-control" placeholder="Например: КД" id="eng_key" />
                        <div class="text-danger small">@error('form.key') {{ $message }} @enderror</div>
                    </div>
                    <div class="mb-3">
                        <label for="eng_price" class="form-label small fw-semibold">Цена за час</label>
                        <div class="input-group">
                            <input type="text" wire:model="form.price" class="form-control" placeholder="0.00" id="eng_price" />
                            <span class="input-group-text text-muted">₽/ч</span>
                        </div>
                        <div class="text-danger small">@error('form.price') {{ $message }} @enderror</div>
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"
                            @click="$dispatch('templateCreateOpenForm')">
                            <i class="bi bi-x-lg me-1"></i>Закрыть
                        </button>
                        <x-blocks.button-submit />
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
