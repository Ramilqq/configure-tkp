<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\TableSettings\Template;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TemplateForm extends Form
{
    public int $id = 0;
    public string $name = '';
    public string $description = '';


    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:20|unique:templates,name,'.$this->id,
            'description' => 'required|min:3|max:200',
        ];
    }

    public function saveForm($id = null)
    {
        $valideate = $this->validate();

        $template = Template::find($this->id);

        if($template)
        {
            $template->update($valideate);
            $template->save();
        }
        else
        {
            $template = Template::create($valideate);
        }
        $this->reset();
        return $template;
    }

    public function editForm($id)
    {
        $template = Template::find($id);
        $this->fill($template);
    }

}
