<div class="table-responsive">

    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h5 class="mb-0 fw-semibold"><i class="bi bi-layout-text-sidebar me-2 text-success"></i>Шаблоны</h5>
    </div>

    <table class="table table-hover table-bordered align-middle mb-0">
        <thead class="table-dark">
            <tr>
                <th style="width:60px;">ID</th>
                <th style="width:280px; position:sticky; left:0; background:#212529; z-index:2;">Имя</th>
                <th>Описание</th>
                <th style="width:210px;" class="text-center">Действия</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $value)

            {{-- Основная строка шаблона --}}
            <tr wire:key="{{ $value->id }}">
                <td class="text-center text-muted">{{ $value->id }}</td>
                <td style="position:sticky; left:0; background:#fff; font-weight:600;">
                    <a class="text-decoration-none text-dark" data-bs-toggle="collapse" href="#template-detail-{{ $value->id }}" role="button">
                        <i class="bi bi-chevron-down me-1 small text-muted"></i>{{ $value->name }}
                    </a>
                </td>
                <td class="text-muted small">{{ $value->description }}</td>
                <td class="text-center">
                    <button title="Изменить шаблон" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#templateModalForm"
                        @click="$dispatch('templateEditOpenForm', {id : {{$value->id}} })">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button title="Удалить шаблон" class="btn btn-outline-danger btn-sm"
                        @click="$dispatch('templateDellete', {id : {{$value->id}} })">
                        <i class="bi bi-trash"></i>
                    </button>
                    <a title="Список продуктов" class="btn btn-outline-success btn-sm"
                        href="{{ route('table-settings.product-list', ['template_id' => $value->id]) }}">
                        <i class="bi bi-view-list"></i>
                    </a>
                    <button title="Добавить опцию" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#templateOptionModalForm"
                        @click="$dispatch('templateOptionInit', {template_id : {{$value->id}} })">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    <button title="Правила цены" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#templatePriceRuleModalForm"
                        @click="$dispatch('templatePriceRuleInit', {template_id : {{$value->id}} })">
                        <i class="bi bi-cash-coin"></i>
                    </button>
                    <a title="Схемы габаритов" class="btn btn-outline-info btn-sm"
                        href="{{ route('table-settings.dimension-schemes', ['template_id' => $value->id]) }}">
                        <i class="bi bi-aspect-ratio"></i>
                    </a>
                </td>
            </tr>

            {{-- Раскрывающийся блок с опциями и правилами цен --}}
            <tr wire:key="detail-{{ $value->id }}">
                <td colspan="4" class="p-0 border-0">
                    <div class="collapse" id="template-detail-{{ $value->id }}">
                        <div class="px-4 py-3 bg-light border-bottom">

                            {{-- Опции шаблона --}}
                            <div class="mb-3">
                                <div class="small fw-semibold text-muted mb-2"><i class="bi bi-sliders me-1"></i>Опции шаблона</div>
                                @if($value->options->isNotEmpty())
                                <table class="table table-sm table-bordered mb-0 bg-white">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th style="width:60px;">ID</th>
                                            <th>Название</th>
                                            <th style="width:90px;" class="text-center">Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($value->options as $option)
                                        <tr>
                                            <td class="text-muted">{{ $option->id }}</td>
                                            <td>{{ $option->name }}</td>
                                            <td class="text-center">
                                                <button title="Изменить" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#templateOptionModalForm"
                                                    @click="$dispatch('templateOptionEditOpenForm', {id : {{$option->id}} })">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button title="Удалить" class="btn btn-outline-danger btn-sm"
                                                    @click="$dispatch('templateOptionDellete', {id : {{$option->id}} })">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <p class="small text-muted mb-0">Нет опций</p>
                                @endif
                            </div>

                            {{-- Правила цены --}}
                            <div>
                                <div class="small fw-semibold text-muted mb-2"><i class="bi bi-cash-coin me-1"></i>Правила цены</div>
                                @if($value->priceRules->isNotEmpty())
                                <table class="table table-sm table-bordered mb-0 bg-white">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th style="width:60px;">ID</th>
                                            <th>Название / параметры</th>
                                            <th style="width:90px;" class="text-center">Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($value->priceRules as $rule)
                                        <tr>
                                            <td class="text-muted">{{ $rule->id }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $rule->name }} <span class="text-muted small">({{ $rule->key }})</span></div>
                                                <div class="text-muted small">
                                                    {{ $rule->enabled ? 'ON' : 'OFF' }} &middot;
                                                    sort={{ $rule->sort }} &middot;
                                                    target={{ $rule->target_field }} &middot;
                                                    mode={{ $rule->mode }}
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button title="Изменить" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#templatePriceRuleModalForm"
                                                    @click="$dispatch('templatePriceRuleEditOpenForm', {id : {{$rule->id}} })">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                                <button title="Удалить" class="btn btn-outline-danger btn-sm"
                                                    @click="$dispatch('templatePriceRuleDelete', {id : {{$rule->id}} })">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @else
                                <p class="small text-muted mb-0">Нет правил цены</p>
                                @endif
                            </div>

                        </div>
                    </div>
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>Нет записей
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
