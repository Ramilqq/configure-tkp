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

                <th scope="col" style="width: 200px;position: sticky;right: 0;">Кнопки</th>
            </tr>
        </thead>
        <tbody>

            @forelse($data as $key => $value)
                <tr wire:key="{{$data[$key]['id']}}" style="vertical-align: middle;"  class="table-active">
                    <th scope="row">{{ $data[$key]['id'] }}</th>
                    <td>{{ $data[$key]['template']['name'] }}  </td>
                    
                    <td style="position: sticky;left: 0;"> <input style="field-sizing: content;" wire:model.lazy="data.{{$key}}.name"  type="text" id="data_name_{{$key}}" /></td>
                    <td style="width: 100%;"><input style="field-sizing: content;" wire:model.lazy="data.{{$key}}.description"  type="text" id="data_description_{{$key}}" /></td>

                    <td>{{ $data[$key]['po'] }}</td>
                    <td>{{ $data[$key]['kd'] }}</td>
                    <td>{{ $data[$key]['pir'] }}</td>
                    <td>{{ $data[$key]['pnr_po'] }}</td>
                    <td>{{ $data[$key]['pnr'] }}</td>
                    <td>{{ $data[$key]['smr_shmr'] }}</td>

                    <td> <input wire:model.change="data.{{$key}}.price_product.price" /> {{ $data[$key]['price_product']['currency']['key']  }} </td>

                    <td style="position: sticky;right: 0;">
                        <button class="btn btn-primary btn-sm"  data-bs-toggle="modal" data-bs-target="#productModalForm" 
                            @click="$dispatch('productEditOpenForm', {id : {{$data[$key]['id']}} })"
                        >Изменить</button>

                        <button class="btn btn-danger btn-sm"
                            @click="$dispatch('productDellete', {id : {{$data[$key]['id']}} })"
                        >Удалить</button>
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

