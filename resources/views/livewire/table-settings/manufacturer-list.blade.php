<div>
    <div class="btn-groups">
        <button title="Добавить производителя" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#manufacturerModalForm"
            @click="$dispatch('manufacturerCreateOpen')"
        >Создать</button>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">ID</th>
                <th scope="col">Наименование</th>
                <th scope="col">Кнопки</th>
            </tr>
        </thead>
        <tbody>

            @forelse($manufacturer as $key => $value)
            <tr>
                <td>{{ $value->id }}</td>
                <td>{{ $value->name }}</td>

                <td>
                    <!-- кнопка изменить производителя -->
                    <button title="Изменить производителя" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#manufacturerModalForm"
                        @click="$dispatch('manufacturerEditOpen', {id : {{$value->id}} })"
                    ><i class="bi bi-pencil-square"></i></button>

                    <!-- кнопка удалить производителя -->
                    <button title="Удалить производителя" class="btn btn-danger btn-sm"
                        wire:click="delete({{$value->id}})"
                        
                    ><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="4">Нет записей</td>
                </tr>
            @endforelse

            <livewire:table-settings.manufacturer-edit />

        </tbody>
    </table>
</div>
