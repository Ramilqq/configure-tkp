<x-layouts.app>
    <div>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModalForm">
            Добавить
        </button>

        <!-- Modal -->
        <div class="modal fade" id="productModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="productModalFormLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="productModalFormLabel">Продукт</h1>
                    </div>
                    <div class="modal-body">
                        <livewire:table-settings.product-modal />
                    </div>
                </div>
            </div>
        </div>

        
        <hr />
        <h1>Продукты</h1>
        
        <livewire:table-settings.product-list  />
    </div>
    

</x-layouts.app>
