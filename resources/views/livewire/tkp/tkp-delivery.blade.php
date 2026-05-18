<div>
    <div class="d-flex align-items-center mb-3">
        <i class="bi bi-truck-front-fill fs-4 text-success me-2"></i>
        <h5 class="mb-0 fw-semibold">Условия оплаты и сроки поставки</h5>
    </div>
    <hr class="mt-0 mb-4">

    <x-blocks.error-message />

    <form wire:submit="saveForm">
        <div class="row g-3">

            <div class="col-12 col-md-4">
                <label for="delivery_time" class="form-label small fw-semibold">Срок поставки, дней</label>
                <input type="text" class="form-control" id="delivery_time" wire:model="form.delivery_params.delivery_time" placeholder="0">
            </div>

            <div class="col-12 col-md-8">
                <label class="form-label small fw-semibold">Место доставки</label>
                <div class="border rounded p-3 bg-light mb-2">
                    @forelse($deliveres as $delivere)
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="radio" value="{{ $delivere['name'] }}" wire:model="form.delivery_params.delivery_location" id="delivere_{{ $delivere['id'] }}">
                        <label class="form-check-label" for="delivere_{{ $delivere['id'] }}">{{ $delivere['name'] }}</label>
                    </div>
                    @empty
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="" wire:model="form.delivery_params.delivery_location" disabled checked>
                        <label class="form-check-label text-muted disabled">Нет данных для отображения</label>
                    </div>
                    @endforelse
                </div>
                <input type="text" class="form-control form-control-sm" id="delivery_location" wire:model="form.delivery_params.delivery_location" placeholder="Или введите адрес вручную">
            </div>

            <div class="col-12">
                <label class="form-label small fw-semibold">Схема оплаты</label>
                <div class="border rounded p-3 bg-light">
                    @forelse($payments_scheme as $payment_scheme)
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="radio" value="{{ $payment_scheme['name'] }}" wire:model="form.delivery_params.payment_scheme" id="payment_scheme_{{ $payment_scheme['id'] }}">
                        <label class="form-check-label" for="payment_scheme_{{ $payment_scheme['id'] }}">{{ $payment_scheme['name'] }}</label>
                    </div>
                    @empty
                    <div class="form-check">
                        <input class="form-check-input" type="radio" value="" wire:model="form.delivery_params.payment_scheme" disabled checked>
                        <label class="form-check-label text-muted disabled">Нет данных для отображения</label>
                    </div>
                    @endforelse
                </div>
            </div>

            <div class="col-12 col-md-8">
                <label for="offer_is_valid" class="form-label small fw-semibold">Предложение действительно, дней</label>
                <small class="text-muted d-block mb-1">(при курсе ЦБ не превышающем 13.07, при неизменности технических требований)</small>
                <input type="text" class="form-control" id="offer_is_valid" wire:model="form.delivery_params.offer_is_valid" placeholder="0">
            </div>

            <div class="col-12 mt-2 d-flex gap-2">
                <a href="{{ route('tkp.sheme.edit', ['id' => $id, 'tkp_version' => $tkp_version]) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Назад
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-arrow-right me-1"></i>Продолжить
                </button>
            </div>

        </div>
    </form>
</div>
