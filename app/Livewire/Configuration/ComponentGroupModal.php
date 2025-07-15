<?php

namespace App\Livewire\Configuration;

use App\Livewire\Forms\Configuration\ComponentGroupForm;
use Livewire\Component;

class ComponentGroupModal extends Component
{
     protected $listeners = ['componentGroupEditOpenForm' => 'componentGroupEditOpenForm', 'componentGroupCreateOpenForm' => 'componentGroupCreateOpenForm'];

    public ComponentGroupForm $form;

    public function saveForm()
    {
        $valideate = $this->form->saveForm();
        $this->dispatch('componentGroupUpdateList');
    }

    public function componentGroupEditOpenForm($id = null)
    {
        $this->form->editForm($id);
    }

    public function componentGroupCreateOpenForm()
    {
        $this->form->reset();
    }

    public function render()
    {
        return view('livewire.configuration.component-group-modal');
    }
}
