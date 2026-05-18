<div>
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-semibold"><i class="bi bi-people me-2 text-success"></i>Пользователи</h5>
        <button title="Добавить" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#userModalForm"
            @click="$dispatch('userCreateOpen')">
            <i class="bi bi-plus-lg me-1"></i>Создать
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>Имя Фамилия</th>
                    <th>E-Mail</th>
                    <th>Телефон</th>
                    <th>Роль</th>
                    <th style="width:90px;" class="text-center">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($user as $key => $value)
                <tr>
                    <td class="text-center text-muted">{{ $value->id }}</td>
                    <td class="fw-semibold">{{ $value->name }}</td>
                    <td>{{ $value->email }}</td>
                    <td>{{ $value->phone }}</td>
                    <td><span class="badge bg-secondary">{{ $value->role }}</span></td>
                    <td class="text-center">
                        <button title="Изменить" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#userModalForm"
                            @click="$dispatch('userEditOpen', {id : {{$value->id}} })">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button title="Удалить" class="btn btn-outline-danger btn-sm"
                            wire:click="delete({{$value->id}})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>Нет записей
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <livewire:table-settings.user-edit />
</div>
