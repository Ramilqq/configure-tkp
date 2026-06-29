<div class="modal fade" id="editModal" tabindex="-1" wire:ignore.self>
    <div class="modal-dialog" style="--bs-modal-width:800px !important">
        <div class="modal-content">
            <div class="modal-header" wire:ignore>
                <h5 class="modal-title" id="modal-title-node"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body" wire:loading.class="opacity-50">

                <div class="row">


                    <div class="col-12">
                        <div style="width: 100%; text-align: center;"><b>Общая информация</b></div>

                        <div class="mb-3">
                            <label for="modal-input1" class="form-label">Название</label>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input1" class="form-control" placeholder="Название или тип">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="modal-input2" class="form-label">Дополнительно</label>
                            <div class="col-auto" style="margin-left:auto;">
                                <input type="text" id="modal-input2" class="form-control" placeholder="Дополнительно">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="getData.manufacturer.other" class="form-label">Завод изготовитель</label>
                            <select
                                wire:key="getData.manufacturer.other"
                                wire:model.defer="getData.manufacturer"
                                id="getData.manufacturer.other"
                                class="form-select"
                                wire:change.debounce.500ms="updateValueManufacturer($event.target.value)"
                            >
                                @foreach($product_manufacturer_select as $manufacturer)
                                    <?php 
                                        $selected = '';
                                        if($manufacturer['name'] == $getData['manufacturer']) {
                                            $selected = 'selected';
                                        }
                                    ?>
                                    <option value="{{ $manufacturer['name'] }}" {{$selected}}>{{ $manufacturer['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="getData.suplier.other" class="form-label">Поставщик</label>
                            <select
                                wire:key="getData.suplier.other"
                                wire:model.defer="getData.suplier"
                                id="getData.suplier.other"
                                class="form-select"
                                wire:change.debounce.500ms="updateValueSuplier($event.target.value)"
                            >
                                @foreach($product_suplier_select as $suplier)
                                    <?php 
                                        $selected = '';
                                        if($suplier['name'] == $getData['suplier']) {
                                            $selected = 'selected';
                                        }
                                    ?>
                                    <option value="{{ $suplier['name'] }}" {{$selected}}>{{ $suplier['name'] }}</option>
                                @endforeach
                            </select>
                        </div>



                        {{-- Правила цены применяются автоматически по опциям продукта --}}
                    </div>
                </div>
            </div>
            
            <div class="modal-body" wire:loading>
                Загрузка фильтра ...
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert" wire:show="message_success">
                    {!! $message_success !!}
                </div>
                <div class="alert alert-danger" role="alert" wire:show="message_error">
                    {!! $message_error !!}
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-danger me-auto" onclick="deleteModalTarget()" data-bs-dismiss="modal" wire:loading.attr="disabled">Удалить</button>
                <button type="submit" class="btn btn-primary" onclick="saveModal()" wire:loading.attr="disabled">Применить</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('editModal').addEventListener('shown.bs.modal', () => {
            // включение и отключение полей в модальном окне при открытии модального окна
            Livewire.dispatch('otherModalOpened', {
                data : {
                    modalTitle: document.getElementById('modal-title-node').textContent.trim(),
                    modalInput1: document.getElementById('modal-input1').value.trim(),
                    modalInput2: document.getElementById('modal-input2').value.trim()
                }
            });
        });
    </script>
</div>
