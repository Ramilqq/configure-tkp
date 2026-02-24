<div>
    <div class="mb-2">
        <a href="{{ route('table-settings.template-list') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Назад к шаблонам
        </a>
    </div>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#dimensionSchemeModalForm"
        @click="$dispatch('dimensionSchemeInit', {template_id : {{ $template_id }} })"
    >
        Добавить схему габаритов
    </button>

    <!-- Modal -->
    <div class="modal fade" id="dimensionSchemeModalForm" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="dimensionSchemeModalFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="dimensionSchemeModalFormLabel">Схемы габаритов</h1>
                </div>
                <div class="modal-body">
                    <livewire:table-settings.template-dimension-scheme-modal wire:key="template-dimension-scheme-modal-{{ $template_id }}" />
                </div>
            </div>
        </div>
    </div>

    <hr />
    <h1>Схемы габаритов по шаблону: {{ $title }}</h1>

    <livewire:table-settings.template-dimension-scheme-list :template_id="$template_id" />
</div>