<div>           
    <div class="btn-groups">
        <button title="Добавить схему оплаты" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentSchemeModalForm"
            @click="$dispatch('paymentSchemeCreateOpen')"
        >Создать</button>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th scope="col" style="width: 50px;">ID</th>
                <th scope="col">Наименование</th>
                <th scope="col">Кнопки</th>
            </tr>
        </thead>
        <tbody>

            @forelse($paymentScheme as $key => $value)
            <tr>
                <td>{{ $value->id }}</td>
                <td>{{ $value->name }}</td>

                <td>
                    <!-- кнопка изменить схему оплаты -->
                    <button title="Изменить схему оплаты" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#paymentSchemeModalForm"
                        @click="$dispatch('paymentSchemeEditOpen', {id : {{$value->id}} })"
                    ><i class="bi bi-pencil-square"></i></button>

                    <!-- кнопка удалить схему оплаты -->
                    <button title="Удалить схему оплаты" class="btn btn-danger btn-sm"
                        wire:click="delete({{$value->id}})"
                        
                    ><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            @empty
                <tr>
                    <td colspan="4">Нет записей</td>
                </tr>
            @endforelse

            <livewire:table-settings.payment-scheme-edit />

        </tbody>
    </table>
</div>