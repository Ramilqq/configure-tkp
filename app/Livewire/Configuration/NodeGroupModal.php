<?php

namespace App\Livewire\Configuration;

use App\Livewire\Forms\Configuration\NodeGroupForm;
use Livewire\Component;

class NodeGroupModal extends Component
{
     protected $listeners = ['nodeGroupEditOpenForm' => 'nodeGroupEditOpenForm', 'nodeGroupCreateOpenForm' => 'nodeGroupCreateOpenForm'];

    public NodeGroupForm $form;

    public function saveForm()
    {
        $valideate = $this->form->saveForm();
        $this->dispatch('nodeGroupUpdateList');
    }

    public function nodeGroupEditOpenForm($id = null)
    {
        $this->form->editForm($id);
    }

    public function nodeGroupCreateOpenForm()
    {
        $this->form->reset();
    }

    public function render()
    {
        return view('livewire.configuration.node-group-modal');
    }
}
