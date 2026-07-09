<?php

namespace App\Livewire\Configuration;

use App\Livewire\Forms\Configuration\NodeForm;
use App\Models\Configuration\Node;
use App\Models\TableSettings\Template;
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

    public function addLabelField()
    {
        $this->form->addLabelField();
    }

    public function dllLabelField($key)
    {
        $this->form->dllLabelField($key);
    }

    public function nudgeAnchorX($key, $delta)
    {
        $this->form->nudgeAnchorX($key, $delta);
    }

    public function nudgeLabelField($key, $axis, $delta)
    {
        $this->form->nudgeLabelField($key, $axis, $delta);
    }

    public function addLabelFieldOption($fieldKey)
    {
        $this->form->addLabelFieldOption($fieldKey);
    }

    public function dllLabelFieldOption($fieldKey, $optionKey)
    {
        $this->form->dllLabelFieldOption($fieldKey, $optionKey);
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
        $templates = Template::get()->toArray();
        return view('livewire.configuration.node-modal', [
            'data' => $this->form->endpoints_arr,
            'templates' => $templates,
        ]);
    }
}
