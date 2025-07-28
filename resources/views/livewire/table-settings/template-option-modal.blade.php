<div>
    <form wire:submit="saveForm">
        <x-blocks.error-message />
        <div class="mb-3">
            <label for="name" class="form-label">Название</label>
            <input type="text" wire:model.lazy="form.name" class="form-control" placeholder="Название" id="name" />
        </div>
        <div class="row mb-3">
            <label for="name" class="form-label">Список значений:</label>
        @forelse($form->fields as $fiekds_key => $fields_val)
            
                <div class="row g-3 align-items-center">
                    <div class="col-auto">
                        <label for="inputPassword6" class="col-form-label">Значение {{(int)$fiekds_key + 1}}</label>
                    </div>
                    <div class="col-auto">
                        <input type="text" class="form-control"
                        wire:model="form.fields.{{$fiekds_key}}"
                        id="field_{{$fiekds_key}}" 
                    />
                    </div>
                    <div class="col-auto" style="margin-left:auto;">
                        <span id="passwordHelpInline" class="form-text">
                        <button wire:click="dllField({{$fiekds_key}})" type="button" class="btn btn-danger"><i class="bi bi-trash"></i></button>
                        </span>
                    </div>
                </div>
            
        @empty
            <p>Ошибка открытия компоненты</p>
        @endforelse
        </div>

        <button wire:click="addField()" type="button" class="btn btn-success w-100"><i class="bi bi-plus-circle"></i></button>



        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="$dispatch('templateCreateOpenForm')">Закрыть</button>
            <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
    </form>
</div>
