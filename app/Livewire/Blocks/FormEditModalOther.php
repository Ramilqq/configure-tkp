<?php

namespace App\Livewire\Blocks;

use App\Models\TableSettings\TemplatePriceRule;
use App\Models\TableSettings\TemplateOption;
use App\Models\Tkp\Manufacturer;
use Livewire\Attributes\On;
use Livewire\Component;

class FormEditModalOther extends Component
{
    public array $product_filter_select = [];
    public array $product_rules_select = [];
    public array $product_manufacturer_select = [];
    public array $getData = [];
    public array $getRules = [];

    public string $message_success = '';
    public string $message_error = '';

    // обновляем список правил цены при выборе шаблона в модальном окне
    #[On('editModalOther.updateFilter')]
    public function updateFilter($template_id, $node_id = null, $conn_id = null, $product_filter_select)
    {
        $this->product_manufacturer_select = Manufacturer::get()->toArray();
        $this->product_rules_select = TemplatePriceRule::where('template_id', $template_id)->get()->toArray();
        $this->product_filter_select = $product_filter_select;
    }

    // обновляем данные в модальном окне при открытии, получая их из конфигурации
    #[On('editModalOther.syncModalData')]
    public function syncModalData($getData, $getRules)
    {
        $this->getData = $getData;
        $this->getRules = $getRules;
    }

    #[On('editModalOther.getMessage')]
    public function getMessage($message_success, $message_error)
    {
        $this->message_success = $message_success;
        $this->message_error = $message_error;
    }

    // обновляем данные в модальном окне при изменении характеристик электродвигателя и пересчитываем номинальный ток
    public function updateData($key, $value)
    {
        foreach ($this->getData as $k => $v) {
            if ($v === '') {
                $this->getData[$k] = null;
            }
        }
        $this->save();
    }

    // сохраняем данные в конфигурации
    public function save()
    {
        $this->dispatch('syncModalDataBack',$this->getData, $this->getRules)->to('configuration.configuration');
    }

    public function mount()
    {
        
    }

    public function render()
    {
        return view('livewire.blocks.form-edit-modal-other');
    }
}
