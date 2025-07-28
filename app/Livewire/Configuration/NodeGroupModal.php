<?php

namespace App\Livewire\Configuration;

use App\Livewire\Forms\Configuration\NodeGroupForm;
use App\Models\TableSettings\Template;
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
        $templates = Template::get()->toArray();
        //dd($templates);
        return view('livewire.configuration.node-group-modal', ['templates' => $templates]);
    }
}
