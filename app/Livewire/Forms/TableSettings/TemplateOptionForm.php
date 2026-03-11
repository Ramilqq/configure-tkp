<?php

namespace App\Livewire\Forms\TableSettings;

use App\Livewire\Forms\BaseForm;
use App\Models\TableSettings\TemplateOption;
use App\Services\StringTranslit;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TemplateOptionForm extends BaseForm
{
    public int $id = 0;
    public int $template_id = 0;
    public int $group_id = 0;
    public string $name = '';
    public string $key = '';
    public ?string $description = '';    
    public array $fields = [];
    
    protected function rules()
    {
        return [
            'template_id' => 'required|numeric|exists:templates,id',
            'group_id' => 'required|numeric|exists:group_options,id',
            'name' => 'required|min:3|max:100',
            'key' => 'required|min:3|max:200',
            'description' => 'nullable|min:3|max:200',
            'fields.*' => 'required|min:1|max:200',
        ];
    }

    public function saveForm($id = null)
    {
        $this->key ?: $this->key = StringTranslit::transliterate($this->name);
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
        //$this->reset();
        return $templateOption;
    }

    public function editForm($id)
    {
        $templateOption = TemplateOption::find($id);
        $this->fill($templateOption);
    }

    public function dllField($key)
    {
        unset($this->fields[$key]);
        $this->fields = array_values($this->fields);
    }

    public function addField()
    {
        $this->fields[] = '';
    }
    
}
