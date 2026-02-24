<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th style="width:60px;">ID</th>
                <th style="width:220px;">Название</th>
                <th>Условия (опции)</th>
                <th>Условия (правила цены)</th>
                <th style="width:110px;">Картинки</th>
                <th style="width:200px;">Действия</th>
            </tr>
        </thead>
        <tbody>
        @forelse($data as $scheme)
            <tr wire:key="scheme-{{ $scheme->id }}" style="vertical-align: middle;">
                <td>{{ $scheme->id }}</td>
                <td>
                    <div><b>{{ $scheme->name }}</b></div>
                    <div class="text-muted small">
                        {{ $scheme->enabled ? 'ON' : 'OFF' }},
                        sort={{ $scheme->sort }},
                        match={{ $scheme->match_mode }}
                    </div>
                </td>
                <td class="small">
                    @if(!empty($scheme->conditions))
                        @foreach($scheme->conditions as $c)
                            <div>
                                <code>{{ $c['option_key'] ?? '' }}</code>
                                {{ $c['op'] ?? '' }}
                                <span class="text-muted">{{ is_array($c['value'] ?? null) ? implode(',', $c['value']) : ($c['value'] ?? '') }}</span>
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
                                {{ $c['op'] ?? '' }}
                                <span class="text-muted">{{ is_array($c['value'] ?? null) ? implode(',', $c['value']) : ($c['value'] ?? '') }}</span>
                            </div>
                        @endforeach
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td>
                    <span class="badge text-bg-primary">{{ count($scheme->images ?? []) }}</span>
                </td>
                <td>
                    <button title="Изменить" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#dimensionSchemeModalForm"
                        @click="$dispatch('dimensionSchemeEditOpenForm', {id : {{ $scheme->id }} })"
                    ><i class="bi bi-pencil-square"></i></button>

                    <button title="Удалить" class="btn btn-danger btn-sm"
                        @click="$dispatch('dimensionSchemeDelete', {id : {{ $scheme->id }} })"
                    ><i class="bi bi-trash"></i></button>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">Нет схем</td></tr>
        @endforelse
        </tbody>
    </table>
</div>