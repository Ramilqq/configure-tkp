<div class="table-responsive">

    <table class="table">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">ID</th>
                <th scope="col"  style="width: 300px; position: sticky;left: 0;">Имя</th>
                <th scope="col"  style="width: 50px;">Валюта</th>
                <th scope="col">Описание</th>
                <th scope="col" style="width: 200px;">Кнопки</th>
            </tr>
        </thead>
        <tbody>

            @forelse($data as $value)
                <tr wire:key="{{$value->id}}" style="vertical-align: middle;"  class="table-active">
                    <th scope="row">{{ $value->id }}</th>
                    <td style="position: sticky;left: 0;">{{ $value->name }}</td>
                    <td>
                        @if($value->currency)
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#currencyModalForm" 
                                @click="$dispatch('currencyEditOpenForm', {template_id : {{$value->id}} })"
                            >
                                {{ $value->currency->key }}
                            </button>
                        @else error! @endif
                    </td>
                    <td>{{ $value->description }}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#templateModalForm" 
                            @click="$dispatch('templateEditOpenForm', {id : {{$value->id}} })"
                        >Изменить <i class="bi bi-0-square"></i></button>

                        <button class="btn btn-danger btn-sm"
                            @click="$dispatch('templateDellete', {id : {{$value->id}} })"
                        >Удалить</button>

                        <a class="btn btn-success btn-sm"
                            href="{{route('table-settings.product-list', ['template_id' => $value->id])}}"
                        >Открыть</a>
                        
                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#templateOptionModalForm"
                            @click="$dispatch('templateOptionInit', {template_id : {{$value->id}} })"
                        >+</button>
                        
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
                                    <th>{{$option->name}} , {{$option->key}}</th>
                                    <th style="width: 200px;">
                                        <button class="btn btn-danger btn-sm"
                                            @click="$dispatch('templateOptionDellete', {id : {{$option->id}} })"
                                        >Удалить</button>
                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#templateOptionModalForm" 
                                            @click="$dispatch('templateOptionEditOpenForm', {id : {{$option->id}} })"
                                        >Изменить <i class="bi bi-0-square"></i></button>
                                    </th>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3">Нет записей</td>
                                </tr>
                                @endforelse
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


