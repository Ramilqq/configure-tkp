<div>
    
    <h1>Контактная информация</h1>
    <hr />
    <x-blocks.error-message />
    
    <form wire:submit="saveForm">
        <input type="hidden" name="form.route">

        <div class="mb-3">
            <label for="project_name" class="form-label">Проект</label>
            <input type="text" class="form-control" id="project_name" wire:model="form.project_name">
        </div>
        <div class="mb-3">
            <label for="client_name" class="form-label">Заказчик</label>
            <input type="text" class="form-control" id="client_name" wire:model="form.client_name">
        </div>
        <div class="mb-3">
            <label for="contract_owner" class="form-label">Владелец договора</label>
            <select class="form-select" wire:model="form.contract_owner" id="contract_owner">

                <option value="" selected>---</option>
                @forelse($contract_owners as $contract_owner)
                    <option value="{{$contract_owner['name']}}" wire:key="{{$contract_owner['id']}}">{{$contract_owner['name']}}</option>
                @empty
                    <option value="">Нет отраслей ...</option>
                @endforelse

            </select>
        </div>
        <div class="mb-3">
            <label for="implementation_object" class="form-label">Объект внедрения</label>
            <input type="text" class="form-control" id="implementation_object" wire:model="form.implementation_object">
        </div>
        <div class="mb-3">
            <label for="industy" class="form-label">Отрасль</label>
            <select class="form-select" wire:model="form.industry" id="industy">

                <option value="" selected>---</option>
                @forelse($industes as $industy)
                    <option value="{{$industy['name']}}" wire:key="{{$industy['id']}}">{{$industy['name']}}</option>
                @empty
                    <option value="">Нет отраслей ...</option>
                @endforelse

            </select>
        </div>

        <button type="submit" class="btn btn-success">Продолжить</button>
    </form>





</div>
