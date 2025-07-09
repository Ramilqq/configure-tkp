<x-layouts.app>

    <!-- Button template modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#templateModalForm">
        Добавить
    </button>
    <!-- Modal template -->
    <div class="modal fade" id="templateModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="templateModalFormLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="templateModalFormLabel">Шаблон</h1>
                </div>
                <div class="modal-body">
                    <livewire:table-settings.template-modal />
                </div>
            </div>
        </div>
    </div>

    <!-- Modal currency -->
    <div class="modal fade" id="currencyModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="currencyModalFormLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="currencyModalFormLabel">Валюта</h1>
                </div>
                <div class="modal-body">
                    <livewire:table-settings.currency-modal />
                </div>
            </div>
        </div>
    </div>

    <hr />
    <h1>Шаблон</h1>
    
    <livewire:table-settings.template-list />

</x-layouts.app>
