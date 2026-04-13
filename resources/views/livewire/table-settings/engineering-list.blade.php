<div>
    <div class="btn-groups">
        <button title="Добавить инженерные данные" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#engineeringModalForm"
            @click="$dispatch('engineeringCreateOpen')"
        >Создать</button>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">ID</th>
                <th scope="col">Наименование</th>
                <th scope="col">Краткое обозночение</th>
                <th scope="col">Цена за час</th>
                <th scope="col">Кнопки</th>
            </tr>
        </thead>
        <tbody>

            @forelse($engineering as $key => $value)
            <tr>
                <td>{{ $value->id }}</td>
                <td>{{ $value->name }}</td>
                <td>{{ $value->key }}</td>
                <td>{{ $value->price }}</td>

                <td>
                    <!-- кнопка изменить инженерные данные -->
                    <button title="Изменить инженерные данные" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#engineeringModalForm"
                        @click="$dispatch('engineeringEditOpen', {id : {{$value->id}} })"
                    ><i class="bi bi-pencil-square"></i></button>

                    <!-- кнопка удалить инженерные данные -->
                    <button title="Удалить инженерные данные" class="btn btn-danger btn-sm"
                        wire:click="delete({{$value->id}})"
                        
                    ><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="4">Нет записей</td>
                </tr>
            @endforelse

            <livewire:table-settings.engineering-edit />

        </tbody>
    </table>
</div>
