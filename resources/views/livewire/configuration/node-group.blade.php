<div>
    <!-- Button configuration-node-group modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nodeGroupModalForm">
        Добавить
    </button>
    <!-- Modal configuration-node-group -->
    <div class="modal fade" id="nodeGroupModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="nodeGroupModalFormLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="nodeGroupModalFormLabel">Группа для компонента</h1>
                </div>
                <div class="modal-body">
                    <livewire:configuration.node-group-modal />
                </div>
            </div>
        </div>
    </div>

    <!-- Modal configuration-node-group -->
    <div class="modal fade" id="nodeModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="nodeModalFormLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="nodeModalFormLabel">Опции для шаблона</h1>
                </div>
                <div class="modal-body">
                    <livewire:configuration.node-modal />
                </div>
            </div>
        </div>
    </div>

    <hr />
    <h1>Компоненты</h1>

    <livewire:configuration.node-group-list />





</div>
