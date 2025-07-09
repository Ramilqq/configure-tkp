<div>
    <form wire:submit="saveForm">
        <div class="mb-3">
            <label for="name" class="form-label">Название</label>
            <input type="text" wire:model="form.name" class="form-control" placeholder="Валюта" id="name" />
            <div class="text-danger">@error('form.name') {{ $message }} @enderror</div>
        </div>

        <div class="mb-3">
            <label for="key" class="form-label">Валюта</label>
            <select class="form-select" wire:model="form.key" id="key">
                <option>---</option>
                
                @forelse($currency as $value)
                    <option wire:key="{{$value['CharCode']}}" value="{{$value['CharCode']}}" @if ($value['CharCode'] == $form->key) selected @endif >{{ $value['Name'] }}</option>
                @empty
                    <option selected>Данных валют нет</option>
                @endforelse
            </select>
            <div class="text-danger">@error('form.key') {{ $message }} @enderror</div>
        </div>

        <div class="mb-3">
            <label for="calc" class="form-label">Изменить цену</label>
            <span class="form-text">если 1, то добавляет к курсу валюты 1 руб.</span>
            <input type="text" wire:model="form.calc" class="form-control" placeholder="+1" id="calc" />
            <div class="text-danger">@error('form.calc') {{ $message }} @enderror</div>
        </div>


        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="$dispatch('currencyCreateOpenForm')">Закрыть</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>
