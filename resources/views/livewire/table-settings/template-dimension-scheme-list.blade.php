<div>
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-semibold"><i class="bi bi-aspect-ratio me-2 text-success"></i>Схемы габаритов</h5>
        <button title="Добавить схему" class="btn btn-success btn-sm"
            data-bs-toggle="modal" data-bs-target="#dimensionSchemeModalForm"
            @click="$dispatch('dimensionSchemeCreateOpenForm')">
            <i class="bi bi-plus-lg me-1"></i>Создать
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-dark">
                <tr>
                    <th style="width:60px;">ID</th>
                    <th style="width:220px;">Название</th>
                    <th>Условия (опции)</th>
                    <th>Условия (правила цены)</th>
                    <th style="width:90px;" class="text-center">Картинки</th>
                    <th style="width:110px;" class="text-center">Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $scheme)
                <tr wire:key="scheme-{{ $scheme->id }}">
                    <td class="text-center text-muted">{{ $scheme->id }}</td>
                    <td>
                        <div class="fw-semibold">{{ $scheme->name }}</div>
                        <div class="text-muted small">
                            <span class="badge {{ $scheme->enabled ? 'bg-success' : 'bg-secondary' }} me-1">
                                {{ $scheme->enabled ? 'ON' : 'OFF' }}
                            </span>
                            sort={{ $scheme->sort }} &middot; match={{ $scheme->match_mode }}
                        </div>
                    </td>
                    <td class="small">
                        @if(!empty($scheme->conditions))
                            @foreach($scheme->conditions as $c)
                                <div>
                                    <code>{{ $c['option_key'] ?? '' }}</code>
                                    <span class="text-muted">{{ $c['op'] ?? '' }}</span>
                                    {{ is_array($c['value'] ?? null) ? implode(', ', $c['value']) : ($c['value'] ?? '') }}
                                </div>
                            @endforeach
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="small">
                        @if(!empty($scheme->rule_conditions))
                            @foreach($scheme->rule_conditions as $c)
                                <div>
                                    <code>{{ $c['rule_key'] ?? '' }}</code>
                                    <span class="text-muted">{{ $c['op'] ?? '' }}</span>
                                    {{ is_array($c['value'] ?? null) ? implode(', ', $c['value']) : ($c['value'] ?? '') }}
                                </div>
                            @endforeach
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="badge bg-primary">{{ count($scheme->images ?? []) }}</span>
                    </td>
                    <td class="text-center">
                        <button title="Изменить" class="btn btn-outline-primary btn-sm"
                            data-bs-toggle="modal" data-bs-target="#dimensionSchemeModalForm"
                            @click="$dispatch('dimensionSchemeEditOpenForm', {id : {{ $scheme->id }} })">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button title="Удалить" class="btn btn-outline-danger btn-sm"
                            @click="$dispatch('dimensionSchemeDelete', {id : {{ $scheme->id }} })">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>Нет схем
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
