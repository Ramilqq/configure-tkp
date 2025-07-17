<?php

namespace App\Livewire\Configuration;

use App\Models\Configuration\Node;
use App\Models\Configuration\NodeGroup;
use Livewire\Component;

class Configuration extends Component
{

    public function render()
    {
        $node = Node::query()->with('nodeGroup')->get()->toArray();
        foreach($node as $node_key => $value_node)
        {
            $node[$node_key]['endpoints'] = json_decode($node[$node_key]['endpoints'], 1);
            $node[$node_key]['defaultName'] = $node[$node_key]['name'];
            $node[$node_key]['defaultExtra'] = '';
            foreach($node[$node_key]['endpoints'] as $anchor_key => $anchor_value)
            {
                $node[$node_key]['endpoints'][$anchor_key]['anchor'] = array_values($node[$node_key]['endpoints'][$anchor_key]['anchor']);
            }
        }
        $groups = NodeGroup::get()->toArray();
        $node = json_encode($node, JSON_UNESCAPED_UNICODE);
        //dd($node);

        return view('livewire.configuration.configuration', ['node' => $node, 'groups' => $groups]);
    }
}
