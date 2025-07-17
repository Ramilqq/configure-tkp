<?php

namespace App\Livewire\Configuration;

use App\Models\Configuration\NodeGroup;
use Livewire\Component;

class NodeGroupList extends Component
{
    protected $listeners = ['nodeGroupUpdateList' => 'render', 'nodeGroupDellete' => 'nodeGroupDellete'];

    public function nodeGroupDellete($id = null)
    {
        NodeGroup::find($id)->delete();
    }

    public function render()
    {
        $data = NodeGroup::query()->with('nodes')->get()->toArray();
        //dd($data);
        return view('livewire.configuration.node-group-list', ['data' => $data]);
    }
}
