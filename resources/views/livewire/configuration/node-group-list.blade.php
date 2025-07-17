<div class="table-responsive">

    <table class="table">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">ID</th>
                <th scope="col"  style="width: 300px; position: sticky;left: 0;">Имя</th>
                <th scope="col" style="width: 200px;">Кнопки</th>
            </tr>
        </thead>
        <tbody>

            @forelse($data as $value)
                <tr wire:key="{{$value['id']}}" style="vertical-align: middle;"  class="table-active">
                    <th scope="row">{{ $value['id'] }}</th>
                    <td style="position: sticky;left: 0;">{{ $value['name'] }}</td>
                    <td>
                        <!-- кнопка изменить группу компонента -->
                        <button title="Изменить группу компонента" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#nodeGroupModalForm"
                            @click="$dispatch('nodeGroupEditOpenForm', {id : {{$value['id']}} })"
                        ><i class="bi bi-pencil-square"></i></button>
                        <!-- кнопка удалить группу компонента -->
                        <button title="Удалить  группу компонента" class="btn btn-danger btn-sm"
                            @click="$dispatch('nodeGroupDellete', {id : {{$value['id']}} })"
                        ><i class="bi bi-trash"></i></button>
                        <!-- кнопка добавить компонент -->
                        <button title="Добавить компонент" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#nodeModalForm" id="nodeModalForm_{{$value['id']}}"
                            @click="$dispatch('nodeInit', {node_group_id : {{$value['id']}} })"
                        ><i class="bi bi-plus-lg"></i></button>
                    </td>
                </tr>

                <tr>
                    <td colspan="3">
                        <table class="table mb-0">
                            <p>Компоненты группы:</p>
                            <tbody>
                                @forelse($value['nodes'] as $node)
                                <tr>
                                    <th style="width: 50px;">{{$node['id']}}</th>
                                    <th>{{$node['name']}}</th>
                                    <th><img src="{{$node['image']}}" /> </th>
                                    <th style="width: 200px;">
                                        <!-- кнопка изменить компонент -->
                                        <button title="Изменить компонент" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#nodeModalForm" 
                                            @click="$dispatch('nodeEditOpenForm', {id : {{$node['id']}} })"
                                        ><i class="bi bi-pencil-square"></i></button>
                                        <!-- кнопка удалить компонент -->
                                        <button title="Удалить компонент" class="btn btn-danger btn-sm" type="button"
                                            @click="$dispatch('nodeDellete', {id : {{$node['id']}} })"
                                            
                                        ><i class="bi bi-trash"></i></button>
                                        
                                    </th>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3">Нет записей</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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


