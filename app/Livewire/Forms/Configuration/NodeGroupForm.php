<?php

namespace App\Livewire\Forms\Configuration;

use App\Models\Configuration\NodeGroup;
use Livewire\Form;

class NodeGroupForm extends Form
{
    public int $id = 0;
    public string $name = '';


    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:20|unique:node_groups,name,'.$this->id,
        ];
    }

    public function saveForm($id = null)
    {
        $valideate = $this->validate();

        $template = NodeGroup::find($this->id);

        if($template)
        {
            $template->update($valideate);
            $template->save();
        }
        else
        {
            $template = NodeGroup::create($valideate);
        }
        $this->reset();
        return $template;
    }

    public function editForm($id)
    {
        $template = NodeGroup::find($id);
        $this->fill($template);
    }
}
