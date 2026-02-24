<div>
    <form wire:submit="saveForm">
        <x-blocks.error-message />
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
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" wire:model="form.name" class="form-control" placeholder="Имя" id="name" />
        </div>

        <div class="mb-3">
            <div class="col">
                <label for="kd" class="form-label">Производитель</label>
                <select class="form-select" wire:model="form.manufacturer_id">
                    @php
                        $product = new \App\Models\TableSettings\Product;
                        
                    @endphp

                    @foreach(($product->getManufacturers() ?? [0 => 'Нет данных']) as $v)
                        <option value="{{ $v['id'] }}" >{{ $v['name'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Описание</label>
            <textarea wire:model="form.description" class="form-control" placeholder="Описание" id="description" style="height: 110px;"></textarea>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="price" class="form-label">Цена</label>
                <input type="text" class="form-control" placeholder="0.0" id="price" wire:model="form.price">
            </div>
            <div class="col">
                <label for="kd" class="form-label">Валюта</label>
                <select class="form-select" wire:model="form.currency">

                    @foreach(($product->allCurrency() ?? ['Нет данных']) as $v)
                        <option value="{{ $v }}" >{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label for="price" class="form-label">Доставка (Руб.)</label>
                <input type="text" class="form-control" placeholder="0.0" id="delivery" wire:model="form.delivery">
            </div>
        </div>

        <div class="row mb-3">
            @foreach(($product->getEngineering() ?? ['Нет данных' => 0]) as $k => $v)
                <div class="col-3">
                    <label for="{{ $k }}" class="form-label">{{ $k }}</label>
                    <input type="number" class="form-control" placeholder="0" id="{{ $k }}" wire:model="form.engineering.{{ $k }}">
                </div>
            @endforeach
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="$dispatch('productCreateOpenForm')">Закрыть</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>
