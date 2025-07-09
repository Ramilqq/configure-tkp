<?php

namespace App\Livewire\TableSettings;

use Livewire\Component;

class Product extends Component
{
    public $template_id_test = 0;

    public function mount($template_id)
    {
        $this->template_id_test = $template_id;
    }

    public function render()
    {
        return view('livewire.table-settings.product', ['template_id' => 9, 'template_id_test' => $this->template_id_test]);
    }
}
