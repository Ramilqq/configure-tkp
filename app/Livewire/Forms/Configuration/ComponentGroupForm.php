<?php

namespace App\Livewire\Forms\Configuration;

use App\Models\Configuration\ComponentGroup;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ComponentGroupForm extends Form
{
    public int $id = 0;
    public string $name = '';


    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:20|unique:cfg_component_groups,name,'.$this->id,
        ];
    }

    public function saveForm($id = null)
    {
        $valideate = $this->validate();

        $template = ComponentGroup::find($this->id);

        if($template)
        {
            $template->update($valideate);
            $template->save();
        }
        else
        {
            $template = ComponentGroup::create($valideate);
        }
        $this->reset();
        return $template;
    }

    public function editForm($id)
    {
        $template = ComponentGroup::find($id);
        $this->fill($template);
    }
}
