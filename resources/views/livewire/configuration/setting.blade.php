<div>
    <!-- Button configuration-component-group modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#componentGroupModalForm">
        Добавить
    </button>
    <!-- Modal configuration-component-group -->
    <div class="modal fade" id="componentGroupModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="componentGroupModalFormLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="componentGroupModalFormLabel">Группа для компонента</h1>
                </div>
                <div class="modal-body">
                    <livewire:configuration.component-group-modal />
                </div>
            </div>
        </div>
    </div>

    <!-- Modal configuration-component-group -->
    <div class="modal fade" id="componentModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="componentModalFormLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="componentModalFormLabel">Опции для шаблона</h1>
                </div>
                <div class="modal-body">
                    <livewire:configuration.component-modal />
                </div>
            </div>
        </div>
    </div>

    <hr />
    <h1>Компоненты</h1>

    <livewire:configuration.component-group-list />





</div>
