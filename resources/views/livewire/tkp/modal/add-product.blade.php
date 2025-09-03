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
                        <div class="mb-3">
                            <label for="new_product.product.name" class="form-label">Название</label>
                            <input type="text" wire:model="form.new_product.product.name" class="form-control" placeholder="Название" id="new_product.product.name" />
                        </div>
                        <div class="mb-3">
                            <label for="new_product.product.description" class="form-label">Описание</label>
                            <input type="text" wire:model="form.new_product.product.description" class="form-control" placeholder="Описание" id="new_product.product.description" />
                        </div>
                        
                        
                        @php
                            $product = new \App\Models\TableSettings\Product;
                        @endphp

                        <div class="row mb-3">
                            @foreach(($product->getEngineering() ?? ['Нет данных' => 0]) as $k => $v)
                                <div class="col-3">
                                    <label for="{{ $k }}" class="form-label">{{ $k }}</label>
                                    <input type="number" class="form-control" placeholder="0" id="po" wire:model="form.new_product.engineering.{{ $k }}">
                                </div>
                            @endforeach
                        </div>



                        <div class="mb-3">
                            <label for="new_product.product.price_product.price" class="form-label">Цена</label>
                            <input type="text" wire:model="form.new_product.product.price" class="form-control" placeholder="Цена" id="new_product.price_product.price" />
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
