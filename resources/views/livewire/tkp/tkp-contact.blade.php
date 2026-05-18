<div>
    <div class="d-flex align-items-center mb-3">
        <i class="bi bi-person-lines-fill fs-4 text-success me-2"></i>
        <h5 class="mb-0 fw-semibold">Контактная информация</h5>
    </div>
    <hr class="mt-0 mb-4">

    <x-blocks.error-message />

    <form wire:submit="saveForm">
        <div class="row g-3">

            <div class="col-12 col-md-6">
                <label for="project_name" class="form-label small fw-semibold">Проект</label>
                <input type="text" class="form-control" id="project_name" wire:model="form.project_name" placeholder="Название проекта">
            </div>

            <div class="col-12 col-md-6">
                <label for="client_name" class="form-label small fw-semibold">Заказчик</label>
                <input type="text" class="form-control" id="client_name" wire:model="form.client_name" placeholder="Название заказчика">
            </div>

            <div class="col-12 col-md-6">
                <label for="contract_owner" class="form-label small fw-semibold">Владелец договора</label>
                <select class="form-select" wire:model="form.contract_owner" id="contract_owner">
                    <option value="" selected>— Выберите —</option>
                    @forelse($contract_owners as $contract_owner)
                        <option value="{{ $contract_owner['name'] }}" wire:key="{{ $contract_owner['id'] }}">{{ $contract_owner['name'] }}</option>
                    @empty
                        <option value="">Нет записей...</option>
                    @endforelse
                </select>
            </div>

            <div class="col-12 col-md-6">
                <label for="implementation_object" class="form-label small fw-semibold">Объект внедрения</label>
                <input type="text" class="form-control" id="implementation_object" wire:model="form.implementation_object" placeholder="Объект внедрения">
            </div>

            <div class="col-12 col-md-6">
                <label for="industy" class="form-label small fw-semibold">Отрасль</label>
                <select class="form-select" wire:model="form.industry" id="industy">
                    <option value="" selected>— Выберите —</option>
                    @forelse($industes as $industy)
                        <option value="{{ $industy['name'] }}" wire:key="{{ $industy['id'] }}">{{ $industy['name'] }}</option>
                    @empty
                        <option value="">Нет отраслей...</option>
                    @endforelse
                </select>
            </div>

            <div class="col-12 mt-2">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-arrow-right me-1"></i>Продолжить
                </button>
            </div>

        </div>
    </form>
</div>
