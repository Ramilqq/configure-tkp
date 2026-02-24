<?php

namespace App\Livewire\TableSettings;

use App\Models\TableSettings\Template;
use Livewire\Component;

class TemplateDimensionScheme extends Component
{
    public int $template_id = 0;
    public string $title = '';

    public function mount($template_id): void
    {
        $this->template_id = (int)$template_id;
        $this->title = (string)(Template::find($this->template_id)?->name ?? '');
    }

    public function render()
    {
        return view('livewire.table-settings.template-dimension-scheme', [
            'template_id' => $this->template_id,
            'title' => $this->title,
        ]);
    }
}
