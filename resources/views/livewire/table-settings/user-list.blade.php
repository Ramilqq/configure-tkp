<div>           
    <div class="btn-groups">
        <button title="Добавить пользователя" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#userModalForm"
            @click="$dispatch('userCreateOpen')"
        >Создать</button>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">ID</th>
                <th scope="col">Имя Фамилие</th>
                <th scope="col">E-Mail</th>
                <th scope="col">Телефон</th>
                <th scope="col">Роль</th>
                <th scope="col">Кнопки</th>
            </tr>
        </thead>
        <tbody>

            @forelse($user as $key => $value)
            <tr>
                <td>{{ $value->id }}</td>
                <td>{{ $value->name }}</td>
                <td>{{ $value->email }}</td>
                <td>{{ $value->phone }}</td>
                <td>{{ $value->role }}</td>

                <td>
                    <!-- кнопка изменить пользователя -->
                    <button title="Изменить пользователя" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userModalForm"
                        @click="$dispatch('userEditOpen', {id : {{$value->id}} })"
                    ><i class="bi bi-pencil-square"></i></button>
                    
                    <!-- кнопка удалить пользователя -->
                    <button title="Удалить пользователя" class="btn btn-danger btn-sm"
                        wire:click="delete({{$value->id}})"
                        
                    ><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="4">Нет записей</td>
                </tr>
            @endforelse

            <livewire:table-settings.user-edit />

        </tbody>
    </table>
</div>