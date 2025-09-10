<div>

    @foreach($tkp as $key => $value)

        {{$value->id}}

    @endforeach


    <table class="table">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">ID</th>
                <!--th scope="col"  style="width: 50px;">Версия</th-->
                <th scope="col">Пользователь</th>
                <th scope="col">Проект</th>
                <th scope="col">Заказчик</th>
                <th scope="col">Комментарий</th>
                <th scope="col">Кнопки</th>
            </tr>
        </thead>
        <tbody>

            @forelse($tkp as $key => $value)
            <tr>
                <td>{{ $value->id }}</td>
                <!--td>{{ $value->tkp_version }}</td-->
                <td>{{ $value->user()->name }}</td>
                <td>{{ $value->project_name }}</td>
                <td>{{ $value->client_name }}</td>
                <td>{{ $value->comments }}</td>

                <td>
                    <!-- кнопка изменить шаблон -->
                    <a title="Изменить шаблон" class="btn btn-primary btn-sm"
                        href="{{route('tkp.calculation.edit', ['id' => $value->id, 'tkp_version' => $value->tkp_version])}}"
                    ><i class="bi bi-pencil-square"></i></a>
                    <!-- кнопка удалить шаблон -->
                    <button title="Удалить шаблон" class="btn btn-danger btn-sm"
                        wire:click="delete({{$value->id}})"
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
