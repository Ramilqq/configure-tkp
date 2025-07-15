<div class="table-responsive">
    <style>

        input {
            background: #e5e5e5;
            border: 0;
        }

        input:focus {
            background:rgb(255, 255, 255);
            box-shadow: 0 0 5px 2px rgba(0, 0, 0, 0.5); /* Добавление свечения */
            outline: none;  /* Удаление контура */
        }

        input[type="text"] {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            field-sizing: content;
        }
    </style>

    <x-blocks.error-message />
    
    <table class="table">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">ID</th>
                <th scope="col" style="width: 100px;">Шаблон</th>
                <th scope="col" style="width: 200px;position: sticky;left: 0;">Имя</th>
                <th scope="col">Описание</th>

                <th scope="col">ПО</th>
                <th scope="col">КД</th>
                <th scope="col">ПИР</th>
                <th scope="col">ПНР ПО</th>
                <th scope="col">ПНР</th>
                <th scope="col">СМР ШМР</th>

                <th scope="col">Цена</th>
                
                @foreach($table_option_col as $col)
                    <th scope="col">{{$col}}</th>
                @endforeach

                <th scope="col" style="width: 200px;position: sticky;right: 0;">Кнопки</th>
            </tr>
        </thead>
        <tbody>

            @forelse($data as $key => $value)
                <tr wire:key="{{$data[$key]['id']}}" style="vertical-align: middle;"  class="table-active">
                    <th scope="row">{{ $data[$key]['id'] }}</th>
                    <td>{{ $data[$key]['template']['name'] }}  </td>
                    
                    <td style="position: sticky;left: 0;"> <input wire:model.lazy="data.{{$key}}.name"  type="text" id="data_name_{{$key}}" /></td>
                    <td style="width: 100%;"><input wire:model.lazy="data.{{$key}}.description"  type="text" id="data_description_{{$key}}" /></td>

                    <td><input wire:model.lazy="data.{{$key}}.po"  type="text" id="data_po_{{$key}}" /></td>
                    <td><input wire:model.lazy="data.{{$key}}.kd"  type="text" id="data_kd_{{$key}}" /></td>
                    <td><input wire:model.lazy="data.{{$key}}.pir"  type="text" id="data_pir_{{$key}}" /></td>
                    <td><input wire:model.lazy="data.{{$key}}.pnr_po"  type="text" id="data_pnr_po_{{$key}}" /></td>
                    <td><input wire:model.lazy="data.{{$key}}.pnr"  type="text" id="data_pnr_{{$key}}" /></td>
                    <td><input wire:model.lazy="data.{{$key}}.smr_shmr"  type="text" id="data_smr_shmr_{{$key}}" /></td>

                    <td> <input id="data_price_product_{{$key}}" wire:model.change="data.{{$key}}.price_product.price" type="text"/></td>

                    @foreach($value['product_option'] as $keyOption => $productOption)
                    <td>
                        
                        <input wire:model.lazy="data.{{$key}}.product_option.{{$keyOption}}.value"  type="text" id="data_{{$key}}_product_option_{{$keyOption}}" />
                    </td>
                    @endforeach


                    <td style="position: sticky;right: 0;">
                        <button title="Изменить продукт" class="btn btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#productModalForm" 
                            @click="$dispatch('productEditOpenForm', {id : {{$data[$key]['id']}} })"
                        ><i class="bi bi-pencil-square"></i></button>

                        <button title="Удалить продукт" class="btn btn-danger btn-sm"
                            @click="$dispatch('productDellete', {id : {{$data[$key]['id']}} })"
                        ><i class="bi bi-trash"></i></button>
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="4">Нет записей</td>
                </tr>
            @endforelse

        </tbody>
    </table>




    
</div>

