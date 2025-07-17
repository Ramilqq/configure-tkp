<?php

namespace App\Livewire\Configuration;

use App\Livewire\Forms\Configuration\NodeForm;
use App\Models\Configuration\Node;
use Livewire\WithFileUploads;
use Livewire\Component;

class NodeModal extends Component
{
    use WithFileUploads;
    
    protected $listeners = [
        'nodeEditOpenForm' => 'nodeEditOpenForm',
        'createOpenForm' => 'createOpenForm' ,
        'nodeInit' => 'nodeInit',
        'nodeDellete' => 'nodeDellete'
    ];

    public NodeForm $form;

    public function init()
    {
        $this->form->init();
    }

    public function addAnchor()
    {
        $this->form->addAnchor();
    }

    public function dllAnchor($key)
    {
        $this->form->dllAnchor($key);
    }

    public function saveForm()
    {
        //dd($this->form);
        //dd(base64_encode(file_get_contents($this->form->image->getRealPath())));
        //dd($this->form->image->getRealPath());
        $valideate = $this->form->saveForm();
        $this->dispatch('nodeGroupUpdateList');
    }
    
    public function nodeInit($node_group_id){
        //dd($template_id);
        $this->form->node_group_id = $node_group_id;
    }

    public function nodeEditOpenForm($id = null)
    {
        $this->form->editForm($id);
    }

    public function createOpenForm()
    {
        $this->form->reset();
    }

    public function nodeDellete($id)
    {
        Node::find($id)->delete();
        $this->dispatch('nodeGroupUpdateList');
    }

    public function mount()
    {
        //$this->init();
    }

    public function render()
    {
        //dd($this->form);
        
        return view('livewire.configuration.node-modal', ['data' => $this->form->endpoints_arr]);
    }
}
