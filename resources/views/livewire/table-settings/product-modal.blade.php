<div>
    <form wire:submit="saveForm">
        
        <div class="mb-3">
            <label for="template_id" class="form-label">Шаблон</label>
            <select class="form-select" wire:model="form.template_id" id="template_id">
                <option>---</option>
                @forelse($template as $value)
                    <option wire:key="{{$value->id}}" value="{{$value->id}}" @if ($form->template_id == $value->id) selected @endif >{{ $value->name }}</option>
                @empty
                    <option selected>Необходимо создать шаблон</option>
                @endforelse
            </select>
            <div class="text-danger">@error('form.template_id') {{ $message }} @enderror</div>
        </div>

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


        <div class="row mb-3">
            <div class="col">
                <label for="po" class="form-label">ПО</label>
                <input type="number" class="form-control" placeholder="ПО" id="po" wire:model="form.po">
                <div class="text-danger">@error('form.po') {{ $message }} @enderror</div>
            </div>
            <div class="col">
                <label for="kd" class="form-label">КД</label>
                <input type="number" class="form-control" placeholder="КД" id="kd" wire:model="form.kd">
                <div class="text-danger">@error('form.kd') {{ $message }} @enderror</div>
            </div>
            <div class="col">
                <label for="pir" class="form-label">ПИР</label>
                <input type="number" class="form-control" placeholder="ПИР" id="pir" wire:model="form.pir">
                <div class="text-danger">@error('form.pir') {{ $message }} @enderror</div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="pnr_po" class="form-label">ПНР ПО</label>
                <input type="number" class="form-control" placeholder="ПНР ПО" id="pnr_po" wire:model="form.pnr_po">
                <div class="text-danger">@error('form.pnr_po') {{ $message }} @enderror</div>
            </div>
            <div class="col">
                <label for="pnr" class="form-label">ПНР</label>
                <input type="number" class="form-control" placeholder="ПНР" id="pnr" wire:model="form.pnr">
                <div class="text-danger">@error('form.pnr') {{ $message }} @enderror</div>
            </div>
            <div class="col">
                <label for="smr_shmr" class="form-label">СМР ШМР</label>
                <input type="number" class="form-control" placeholder="СМР ШМР" id="smr_shmr" wire:model="form.smr_shmr">
                <div class="text-danger">@error('form.smr_shmr') {{ $message }} @enderror</div>
            </div>
        </div>


        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="$dispatch('productCreateOpenForm')">Закрыть</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>
