<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\TemplateForm;
use Livewire\Component;

class TemplateModal extends Component
{
    protected $listeners = ['templateEditOpenForm' => 'templateEditOpenForm', 'templateCreateOpenForm' => 'templateCreateOpenForm'];

    public TemplateForm $form;

    public function saveForm()
    {
        $valideate = $this->form->saveForm();
        $this->dispatch('templateUpdateList');
    }

    public function templateEditOpenForm($id = null)
    {
        $this->form->editForm($id);
    }

    public function templateCreateOpenForm()
    {
        $this->form->reset();
    }
    
    public function render()
    {
        return view('livewire.table-settings.template-modal');
    }
}
