<div>
    <h1>Требования по условиям оплаты и срокам поставки</h1>
    <hr />
    <x-blocks.error-message />

    <form wire:submit="saveForm">
        <input type="hidden" name="form.delivery_params.route">
        
        <div class="mb-3">
            <label for="delivery_time" class="form-label">Срок поставки, дней</label>
            <input type="text" class="form-control" id="delivery_time" wire:model="form.delivery_params.delivery_time">
        </div>

        <div class="mb-3">
            <label for="contract_owner" class="form-label">Место доставки</label>

            @forelse($deliveres as $delivere)
            <div class="form-check">
                <input class="form-check-input" type="radio" value="{{$delivere['name']}}" wire:model="form.delivery_params.delivery_location" id="delivere_{{$delivere['id']}}">
                <label class="form-check-label" for="delivere_{{$delivere['id']}}">
                    {{$delivere['name']}}
                </label>
            </div>
             @empty
                <input class="form-check-input" type="radio" value="" wire:model="form.delivery_params.delivery_location" disabled checked >
                <label class="form-check-label"disabled>
                    Нет данных для отображения
                </label>
            @endforelse

            <input type="text" class="form-control" id="delivery_location" wire:model="form.delivery_params.delivery_location">

        </div>
        
        <div class="mb-3">
            <label for="industy" class="form-label">Схема оплаты</label>
            @forelse($payments_scheme as $payment_scheme)
            <div class="form-check">
                <input class="form-check-input" type="radio" value="{{$payment_scheme['name']}}" wire:model="form.delivery_params.payment_scheme" id="payment_scheme_{{$payment_scheme['id']}}">
                <label class="form-check-label" for="payment_scheme_{{$payment_scheme['id']}}">
                    {{$payment_scheme['name']}}
                </label>
            </div>
             @empty
                <input class="form-check-input" type="radio" value="" wire:model="form.delivery_params.payment_scheme" disabled checked >
                <label class="form-check-label"disabled>
                    Нет данных для отображения
                </label>
            @endforelse
        </div>

        <div class="mb-3">
            <label for="offer_is_valid" class="form-label">Предложение действительно, дней (при курсе ЦБ не превышающем 13.07, при неизменности технических требований.)</label>
            <input type="text" class="form-control" id="offer_is_valid" wire:model="form.delivery_params.offer_is_valid">
        </div>

        <a href="{{ route('tkp.sheme.edit', ['id' => $id, 'tkp_version' => $tkp_version]) }}" type="button" class="btn btn-secondary">Назад</a>
        <button type="submit" class="btn btn-success" >Продолжить</button>
    </form>
</div>
