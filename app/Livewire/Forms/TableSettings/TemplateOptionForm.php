<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\TableSettings\TemplateOption;
use App\Services\StringTranslit;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TemplateOptionForm extends Form
{
    public int $id = 0;
    public int $template_id = 0;
    public string $name = '';
    public string $key = '';

    protected function rules()
    {
        return [
            'template_id' => 'required|numeric|exists:templates,id',
            'name' => 'required|min:3|max:20',
            'key' => 'required|min:3|max:200',
        ];
    }

    public function saveForm($id = null)
    {
        $this->key = StringTranslit::transliterate($this->name);
        $valideate = $this->validate();

        $templateOption = TemplateOption::find($this->id);

        if($templateOption)
        {
            $templateOption->update($valideate);
            $templateOption->save();
        }
        else
        {
            $templateOption = TemplateOption::create($valideate);
        }
        $this->reset();
        return $templateOption;
    }

    public function editForm($id)
    {
        $templateOption = TemplateOption::find($id);
        $this->fill($templateOption);
    }


    
}
