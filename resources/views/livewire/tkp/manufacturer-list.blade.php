<div>
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
                    <!-- кнопка изменить шаблон -->
                    <button title="Изменить производителя" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#manufacturerModalForm"
                        @click="$dispatch('manufacturerEditOpen', {id : {{$value->id}} })"
                    ><i class="bi bi-pencil-square"></i></button>

                    <!-- кнопка удалить шаблон -->
                    <button title="Удалить шаблон" class="btn btn-danger btn-sm"
                        wire:click="delete({{$value->id}})"
                        disabled
                    ><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="4">Нет записей</td>
                </tr>
            @endforelse

            <livewire:tkp.manufacturer-edit />

        </tbody>
    </table>
</div>
