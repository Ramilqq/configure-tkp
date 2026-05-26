<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\TemplateOptionForm;
use App\Models\TableSettings\GroupOption;
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

    public $groups = [];

    public function templateOptionCreate($template_id){
        
        $this->form->template_id = $template_id;
    }

    public function templateOptionInit($template_id){
        $this->form->reset();
        $this->mount();
        $this->form->template_id = $template_id;
    }

    public function templateOptionEditOpenForm($id = null)
    {
        $this->form->editForm($id);
    }

    public function saveForm()
    {   
        $valideate = $this->form->saveForm();
        session()->flash('status', 'Данные успешно сохранены.');
        $this->dispatch('templateUpdateList');
        
    }

    public function templateOptionDellete($id = null)
    {
        TemplateOption::find($id)->delete();
        $this->dispatch('templateUpdateList');
    }
    
    public function dllField($key)
    {
        $this->form->dllField($key);
    }

    public function addField()
    {
        $this->form->addField();
    }

    public function mount()
    {
        $this->form->fields[] = '';
        $this->groups = GroupOption::all()->toArray();
    }

    public function render()
    {
        return view('livewire.table-settings.template-option-modal');
    }
}
