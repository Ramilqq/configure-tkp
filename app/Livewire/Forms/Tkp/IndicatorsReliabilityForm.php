<?php

namespace App\Livewire\Forms\Tkp;

use App\Models\Configuration\Configuration;
use Livewire\Attributes\Validate;
use Livewire\Form;

class IndicatorsReliabilityForm extends Form
{
    public int $tkp_version = 0;
    public string $hash = '';
    public array $indicators_reliability = [];

    public string $message_success = '';
    public string $message_error = '';

    protected function rules()
    {
        return [
            'indicators_reliability.*.group_name'           => 'required',
            'indicators_reliability.*.indicators.*.name'    => 'required',
            'indicators_reliability.*.indicators.*.value'   => 'required',
        ];
    }

    public function saveForm()
    {
        $validatedData = $this->validate();
        
        $configuration = Configuration::query()->where('tkp_version', $this->tkp_version)->first();
        $saved_schema = $configuration->saved_schema;

        foreach ($saved_schema['nodes'] as &$node) {
            
            if ($node['product']['fr_hash'] == $this->hash) {
                $node['product']['indicators_reliability'] = $validatedData['indicators_reliability'];
                break;
            }
        }
        $configuration->saved_schema = $saved_schema;
        
        if ($configuration->save()) {
            $this->message_success = 'Показатели надежности успешно сохранены';
        } else {
            $this->message_error = 'Ошибка при сохранении показателей надежности';
        }
    }

    public function openForm()
    {   
        $this->message_success = '';
        $this->message_error = '';

        $configuration = Configuration::query()->where('tkp_version', $this->tkp_version)->first();

        foreach ($configuration->saved_schema['nodes'] as $node) {
            
            if ($node['product']['fr_hash'] == $this->hash) {
                $this->indicators_reliability = $node['product']['indicators_reliability'] ?? [];
                break;
            }
        }
    }

}
