<?php

namespace App\Livewire\TableSettings;

use App\Models\TableSettings\Template as ModelsTemplate;
use Livewire\Component;

class TemplateList extends Component
{

    protected $listeners = ['templateUpdateList' => 'render', 'templateDellete' => 'templateDellete'];

    public function templateDellete($id = null)
    {
        ModelsTemplate::find($id)->delete();
    }

    

    public function render()
    {   
        $data = ModelsTemplate::query()->with('currency')->with('options')->get();
        //dd( $data);
        return view('livewire.table-settings.template-list', ['data' => $data]);
    }
}
