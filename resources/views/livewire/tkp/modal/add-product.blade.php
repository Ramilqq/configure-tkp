<div>
    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProductForm" @click="$dispatch('addProductOpenForm', {product_id : 0 })"><i class="bi bi-plus-lg"></i></button>

    <div class="modal fade" id="addProductForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addProductFormLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addProductFormLabel">Добавить продукт</h1>
                </div>
                <div class="modal-body">
                    <form wire:submit="saveForm">
                        <x-blocks.error-message />
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="new_product.product.name" class="form-label">Название</label>
                                    <input type="text" wire:model="form.new_product.product.name" class="form-control" placeholder="Название" id="new_product.product.name" />
                                </div>
                                <div class="mb-3">
                                    <label for="new_product.product.description" class="form-label">Описание</label>
                                    <textarea name="new_product.product.description" id="new_product.product.description" class="form-control" placeholder="Описание" wire:model="form.new_product.product.description"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="new_product.product.manufacturer" class="form-label">Производитель</label>
                                    <input type="text" wire:model="form.new_product.product.manufacturer" class="form-control" placeholder="Производитель" id="new_product.product.manufacturer" />
                                </div>
                            </div>
                            <div class="col-6">
                                @php
                                    $product = new \App\Models\TableSettings\Product;
                                @endphp

                                <div class="row mb-3 align-items-end">
                                    @foreach(($product->getEngineering() ?? ['Нет данных' => 0]) as $k => $v)
                                        <div class="col-3">
                                            <label for="{{ $k }}" class="form-label">{{ $k }}<span class="small">(Час)</span></label>
                                            <input id="{{ $k }}" type="number" class="form-control" placeholder="0" wire:model="form.new_product.product.engineering.{{ $k }}">
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row mb-3">
                                    <div class="col-4">
                                        <label for="new_product.product.price_product.price" class="form-label">Цена продукта</label>
                                        <input type="text" wire:model="form.new_product.product.price" class="form-control" placeholder="0.0" id="new_product.price_product.price" />
                                    </div>
                                    
                                    <div class="col-4">
                                        <label for="new_product.product.currency" class="form-label">Валюта</label>
                                        <select class="form-select" wire:model.lazy="form.new_product.product.currency" id="new_product.product.currency" >
                                            @forelse($banks as $bank)
                                                <option value="{{$bank['CharCode']}}" wire:key="bank_{{$bank['NumCode']}}">{{$bank['CharCode']}}</option>
                                            @empty
                                                <option value="">---</option>
                                            @endforelse
                                                <option value="RUB">RUB</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-4">
                                        <label for="new_product.product.currency_val" class="form-label">Курс</label>
                                        <input type="text" wire:model="form.new_product.product.currency_val" class="form-control" placeholder="Курс" id="new_product.product.currency_val" />
                                    </div>
                                </div>
                                <hr>
                                <div class=" row mb-3">
                                    <div class="col-12">
                                        <label for="new_product.product.delivery" class="form-label">Доставка <span class="small">(RUB)</span></label>
                                        <input type="text" wire:model="form.new_product.product.delivery" class="form-control" placeholder="0.0" id="new_product.product.delivery" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <x-blocks.button-close />
                            <x-blocks.button-submit />
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
