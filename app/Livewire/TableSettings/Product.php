<?php

namespace App\Livewire\TableSettings;

use App\Models\TableSettings\Template;
use Livewire\Component;

class Product extends Component
{
    public $template_id = 0;
    public $title = '';

    public function mount($template_id)
    {
        $this->template_id = $template_id;
        $this->title = Template::find($template_id)->name;
    }

    public function render()
    {
        return view('livewire.table-settings.product', ['template_id' => $this->template_id, 'title' => $this->title]);
    }
}
