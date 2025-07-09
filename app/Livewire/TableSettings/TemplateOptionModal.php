<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\TemplateOptionForm;
use App\Models\TableSettings\TemplateOption;
use Livewire\Component;

class TemplateOptionModal extends Component
{
    public TemplateOptionForm $form;

    protected $listeners = [
        'templateOptionInit' => 'templateOptionInit', 
        'templateOptionEditOpenForm' => 'templateOptionEditOpenForm', 
        'templateOptionDellete' => 'templateOptionDellete',
    ];

    public function templateOptionCreate($template_id){
        $this->form->template_id = $template_id;
    }

    public function templateOptionInit($template_id){
        //dd($template_id);
        $this->form->template_id = $template_id;
    }

    public function templateOptionEditOpenForm($id = null)
    {
        $this->form->editForm($id);
    }

    public function saveForm()
    {   
        $this->form->key = $this->form->name;
        $valideate = $this->form->saveForm();
        $this->dispatch('templateUpdateList');
    }

    public function templateOptionDellete($id = null)
    {
        TemplateOption::find($id)->delete();
        $this->dispatch('templateUpdateList');
    }

    public function render()
    {
        return view('livewire.table-settings.template-option-modal');
    }
}
