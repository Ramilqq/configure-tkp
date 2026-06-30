<div>
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-semibold"><i class="bi bi-folder2-open me-2 text-success"></i>Список ТКП</h5>
        <a href="{{ route('tkp.contact') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg me-1"></i>Создать ТКП
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>Дата создания</th>
                    <th>Дата изменения</th>
                    <th>Менеджер</th>
                    <th>Последнее изменение</th>
                    <th>Проект</th>
                    <th>Заказчик</th>
                    <th>Комментарий</th>
                    <th style="width:100px;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tkp as $key => $value)
                <tr>
                    <td class="text-center fw-semibold text-muted">{{ $value->id }}</td>
                    <td class="small">{{ $value->created_at }}</td>
                    <td class="small">{{ $value->updated_at }}</td>
                    <td>{{ $value->user()?->name }}</td>
                    <td>{{ $value->updateUser()?->name ?? '—' }}</td>
                    <td>{{ $value->project_name }}</td>
                    <td>{{ $value->client_name }}</td>
                    <td class="text-muted small">{{ $value->comments }}</td>
                    <td class="text-center">
                        <a title="Открыть расчёт" class="btn btn-primary btn-sm"
                            href="{{ route('tkp.calculation.edit', ['id' => $value->id, 'tkp_version' => $value->tkp_version]) }}"
                        ><i class="bi bi-pencil-square"></i></a>
                        <button title="Удалить" class="btn btn-danger btn-sm"
                            wire:click="delete({{ $value->id }})"
                            wire:confirm="Удалить этот ТКП?"
                        ><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>Нет записей
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
