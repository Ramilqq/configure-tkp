<div>
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-semibold"><i class="bi bi-diagram-3 me-2 text-success"></i>Группы компонентов конфигуратора</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:60px;">ID</th>
                    <th>Имя</th>
                    <th style="width:130px;" class="text-center">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $value)

                {{-- Строка группы --}}
                <tr wire:key="{{ $value['id'] }}">
                    <td class="text-center text-muted">{{ $value['id'] }}</td>
                    <td>
                        <a class="text-decoration-none fw-semibold text-dark d-flex align-items-center gap-1"
                            data-bs-toggle="collapse" href="#nodes-{{ $value['id'] }}" role="button">
                            <i class="bi bi-chevron-down small text-muted"></i>
                            {{ $value['name'] }}
                        </a>
                    </td>
                    <td class="text-center">
                        <button title="Изменить группу" class="btn btn-outline-primary btn-sm"
                            data-bs-toggle="modal" data-bs-target="#nodeGroupModalForm"
                            @click="$dispatch('nodeGroupEditOpenForm', {id : {{$value['id']}} })">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button title="Удалить группу" class="btn btn-outline-danger btn-sm"
                            @click="$dispatch('nodeGroupDellete', {id : {{$value['id']}} })">
                            <i class="bi bi-trash"></i>
                        </button>
                        <button title="Добавить компонент" class="btn btn-outline-success btn-sm"
                            data-bs-toggle="modal" data-bs-target="#nodeModalForm"
                            @click="$dispatch('nodeInit', {node_group_id : {{$value['id']}} })">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </td>
                </tr>

                {{-- Раскрывающийся список компонентов --}}
                <tr wire:key="nodes-{{ $value['id'] }}">
                    <td colspan="3" class="p-0 border-0">
                        <div class="collapse" id="nodes-{{ $value['id'] }}">
                            <div class="px-4 py-3 bg-light border-bottom">
                                <div class="small fw-semibold text-muted mb-2">
                                    <i class="bi bi-boxes me-1"></i>Компоненты группы
                                </div>
                                @if(!empty($value['nodes']))
                                <table class="table table-sm table-bordered mb-0 bg-white align-middle">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th style="width:60px;">ID</th>
                                            <th>Название</th>
                                            <th style="width:100px;" class="text-center">Превью</th>
                                            <th style="width:90px;" class="text-center">Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($value['nodes'] as $node)
                                        <tr>
                                            <td class="text-muted">{{ $node['id'] }}</td>
                                            <td class="fw-semibold">{{ $node['name'] }}</td>
                                            <td class="text-center">
                                                @if(!empty($node['image']))
                                                <img src="{{ $node['image'] }}" alt="{{ $node['name'] }}"
                                                    style="max-height:40px; max-width:80px; object-fit:contain;">
                                                @else
                                                <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button title="Изменить" class="btn btn-outline-primary btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#nodeModalForm"
                                                    @click="$dispatch('nodeEditOpenForm', {id : {{$node['id']}} })">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button title="Удалить" class="btn btn-outline-danger btn-sm"
                                                    @click="$dispatch('nodeDellete', {id : {{$node['id']}} })">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <p class="small text-muted mb-0">Нет компонентов в группе</p>
                                @endif
                            </div>
                        </div>
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
</div>
