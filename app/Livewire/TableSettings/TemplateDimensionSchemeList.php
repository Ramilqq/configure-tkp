<?php

namespace App\Livewire\TableSettings;

use App\Models\TableSettings\TemplateDimensionScheme;
use Livewire\Component;

class TemplateDimensionSchemeList extends Component
{
    protected $listeners = [
        'dimensionSchemeUpdateList' => '$refresh',
    ];

    public int $template_id = 0;

    public function render()
    {
        $data = TemplateDimensionScheme::query()
            ->where('template_id', $this->template_id)
            ->with('images')
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return view('livewire.table-settings.template-dimension-scheme-list', [
            'data' => $data,
        ]);
    }
}
