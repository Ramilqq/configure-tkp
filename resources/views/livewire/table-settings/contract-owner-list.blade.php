<div>
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-semibold"><i class="bi bi-person-badge me-2 text-success"></i>Владелец договора</h5>
        <button title="Добавить" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#contactOwnerModalForm"
            @click="$dispatch('contactOwnerCreateOpen')">
            <i class="bi bi-plus-lg me-1"></i>Создать
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>Наименование</th>
                    <th style="width:90px;" class="text-center">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contactOwner as $key => $value)
                <tr>
                    <td class="text-center text-muted">{{ $value->id }}</td>
                    <td>{{ $value->name }}</td>
                    <td class="text-center">
                        <button title="Изменить" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#contactOwnerModalForm"
                            @click="$dispatch('contactOwnerEditOpen', {id : {{$value->id}} })">
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
                    <td colspan="3" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>Нет записей
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <livewire:table-settings.contract-owner-edit />
</div>
