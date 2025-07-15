<?php

namespace App\Livewire\Configuration;

use App\Models\Configuration\ComponentGroup;
use Livewire\Component;

class ComponentGroupList extends Component
{
    protected $listeners = ['componentGroupUpdateList' => 'render', 'componentGroupDellete' => 'componentGroupDellete'];

    public function componentGroupDellete($id = null)
    {
        ComponentGroup::find($id)->delete();
    }

    public function render()
    {
        $data = ComponentGroup::query()->with('components')->get()->toArray();
        //dd($data);
        return view('livewire.configuration.component-group-list', ['data' => $data]);
    }
}
