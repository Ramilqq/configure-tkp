<div>
    <form wire:submit="saveForm">
        <x-blocks.error-message />
        <div class="mb-3">
            <label for="name" class="form-label">Название</label>
            <input type="text" wire:model.lazy="form.name" class="form-control" placeholder="Название" id="name" />
        </div>
        <div class="mb-3">
            <label for="key" class="form-label">Ключ</label>
            <input type="text" wire:model.lazy="form.key" class="form-control" placeholder="Ключ" id="key" />
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Поле для описания</label>
            <input type="text" wire:model.lazy="form.description" class="form-control" placeholder="Описание" id="description" />
        </div>
        

        <div class="mb-3">
            <label for="group_id" class="form-label">Группа опции</label>
            <select class="form-select" wire:model="form.group_id" id="group_id">
                <option value="0" selected>Нет группы</option>
                @forelse($groups as $value)
                    <option wire:key="{{$value['id']}}" value="{{$value['id']}}">{{ $value['name'] }}</option>
                @empty
                    <option value="0" selected>Нет группы</option>
                @endforelse
            </select>
        </div>

        <div class="row mb-3">
            <label for="name" class="form-label">Список значений:</label>
            
            <div class="row g-3 align-items-center">
                <div class="col-2">
                    #
                </div>
                <div class="col-5">
                    Значение
                </div>

                <div class="col-2" style="margin-left:auto;">
                </div>
            </div>


        @forelse($form->fields as $fiekds_key => $fields_val)
                <div class="row g-3 align-items-center">
                    <div class="col-2">
                        <label for="inputPassword6" class="col-form-label">№ {{(int)$fiekds_key + 1}}</label>
                    </div>
                    <div class="col-5">
                        <input type="text" class="form-control"
                            wire:model="form.fields.{{$fiekds_key}}"
                            id="field_{{$fiekds_key}}" 
                        />
                    </div>

                    <div class="col-2" style="margin-left:auto;">
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
