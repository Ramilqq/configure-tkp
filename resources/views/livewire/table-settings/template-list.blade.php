<div class="table-responsive">

    <table class="table">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">ID</th>
                <th scope="col"  style="width: 300px; position: sticky;left: 0;">Имя</th>
                <th scope="col">Описание</th>
                <th scope="col" style="width: 200px;">Кнопки</th>
            </tr>
        </thead>
        <tbody>

            @forelse($data as $value)
                <tr wire:key="{{$value->id}}" style="vertical-align: middle;"  class="table-active">
                    <th scope="row">{{ $value->id }}</th>
                    <td style="position: sticky;left: 0;">{{ $value->name }}</td>
                    <td>{{ $value->description }}</td>
                    <td>
                        <!-- кнопка изменить шаблон -->
                        <button title="Изменить шаблон" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#templateModalForm"
                            @click="$dispatch('templateEditOpenForm', {id : {{$value->id}} })"
                        ><i class="bi bi-pencil-square"></i></button>
                        <!-- кнопка удалить шаблон -->
                        <button title="Удалить шаблон" class="btn btn-danger btn-sm"
                            @click="$dispatch('templateDellete', {id : {{$value->id}} })"
                        ><i class="bi bi-trash"></i></button>
                        <!-- кнопка открыть таблицу продукта -->
                        <a  title="Открыть таблицу продуктов" class="btn btn-success btn-sm"
                            href="{{route('table-settings.product-list', ['template_id' => $value->id])}}"
                        ><i class="bi bi-view-list"></i></a>
                        <!-- кнопка добавить опцию -->
                        <button title="Добавить опцию" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#templateOptionModalForm"
                            @click="$dispatch('templateOptionInit', {template_id : {{$value->id}} })"
                        ><i class="bi bi-plus-lg"></i></button>
                        <!-- кнопка добавить правило цены -->
                        <button title="Правила цены" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#templatePriceRuleModalForm"
                            @click="$dispatch('templatePriceRuleInit', {template_id : {{$value->id}} })"
                        ><i class="bi bi-cash-coin"></i></button>
                        <!-- кнопка схем габаритов -->
                        <a title="Схемы габаритов" class="btn btn-info btn-sm"
                            href="{{route('table-settings.dimension-schemes', ['template_id' => $value->id])}}"
                        ><i class="bi bi-aspect-ratio"></i></a>
                    </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <table class="table mb-0">
                            <h5>Опции шаблона</h5>
                            <tbody>
                                @forelse($value->options as $option)
                                <tr>
                                    <th style="width: 50px;">{{$option->id}}</th>
                                    <td>{{$option->name}}</td>
                                    <td style="width: 200px;">
                                        <!-- кнопка изменить опцию -->
                                        <button title="Изменить опцию" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#templateOptionModalForm" 
                                            @click="$dispatch('templateOptionEditOpenForm', {id : {{$option->id}} })"
                                        ><i class="bi bi-pencil-square"></i></button>
                                        <!-- кнопка удалить опцию -->
                                        <button title="Удалить опцию" class="btn btn-danger btn-sm"
                                            @click="$dispatch('templateOptionDellete', {id : {{$option->id}} })"
                                        ><i class="bi bi-trash"></i></button>
                                        
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3">Нет записей</td>
                                </tr>
                                @endforelse
                                <tr>
                                    <td colspan="5">
                                        <table class="table mb-0">
                                            <h5>Правила цены</h5>
                                            <tbody>
                                            @forelse($value->priceRules as $rule)
                                                <tr>
                                                    <th style="width:50px;">{{ $rule->id }}</th>
                                                    <td>
                                                        <div><b>{{ $rule->name }}</b> <span class="text-muted">({{ $rule->key }})</span></div>
                                                        <div class="text-muted small">
                                                            {{ $rule->enabled ? 'ON' : 'OFF' }},
                                                            sort={{ $rule->sort }},
                                                            target={{ $rule->target_field }},
                                                            mode={{ $rule->mode }}
                                                        </div>
                                                    </td>
                                                    <td style="width:200px;">
                                                        <button title="Изменить" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#templatePriceRuleModalForm"
                                                            @click="$dispatch('templatePriceRuleEditOpenForm', {id : {{$rule->id}} })"
                                                        ><i class="bi bi-pencil-square"></i></button>

                                                        <button title="Удалить" class="btn btn-danger btn-sm"
                                                            @click="$dispatch('templatePriceRuleDelete', {id : {{$rule->id}} })"
                                                        ><i class="bi bi-trash"></i></button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3">Нет правил</td></tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Нет записей</td>
                </tr>
            @endforelse

        </tbody>
    </table>
</div>


