<div>
    <div class="modal fade" id="editIndicatorsReliability" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editIndicatorsReliabilityLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editIndicatorsReliabilityLabel">Редактировать показатели надежности</h1>
                </div>
                <div class="modal-body">
                    <form wire:submit="saveForm">
                        <x-blocks.error-message />
                        
                        @if($form->indicators_reliability)
                            
                            @foreach($form->indicators_reliability[0]['indicators'] as $key => $value)

                                <div class="mb-3">
                                    <label for="form.indicators_reliability.0.indicators.{{$key}}.value" class="form-label">{{$value['name']}}</label>
                                    
                                    <textarea 
                                        wire:model="form.indicators_reliability.0.indicators.{{$key}}.value"
                                        class="form-control"
                                        placeholder=""
                                        id="form.indicators_reliability.0.indicators.{{$key}}.value" 
                                        wire:key="form-indicators_reliability-0-indicators-{{$key}}-value">
                                    </textarea>
                                </div>
                            @endforeach

                        @else 
                            <p>Показатели надежности не найдены для данного продукта</p>
                        @endif


                        <div class="alert alert-success" role="alert" wire:show="form.message_success">
                            {!! $form->message_success !!}
                        </div>
                        <div class="alert alert-danger" role="alert" wire:show="form.message_error">
                            {!! $form->message_error !!}
                        </div>

                        <div class="modal-footer">
                            <x-blocks.button-close />
                            <x-blocks.button-submit />
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
