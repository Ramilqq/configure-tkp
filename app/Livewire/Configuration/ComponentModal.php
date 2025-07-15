<?php

namespace App\Livewire\Configuration;

use App\Livewire\Forms\Configuration\ComponentForm;
use App\Models\Configuration\Component;
use Livewire\WithFileUploads;
use Livewire\Component as LivewireComponent;

class ComponentModal extends LivewireComponent
{
    use WithFileUploads;
    
    protected $listeners = [
        'componentEditOpenForm' => 'componentEditOpenForm',
        'createOpenForm' => 'createOpenForm' ,
        'componentInit' => 'componentInit',
        'componentDellete' => 'componentDellete'
    ];

    public ComponentForm $form;

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
        $this->dispatch('componentGroupUpdateList');
    }
    
    public function componentInit($cfg_component_group_id){
        //dd($template_id);
        $this->form->cfg_component_group_id = $cfg_component_group_id;
    }

    public function componentEditOpenForm($id = null)
    {
        $this->form->editForm($id);
    }

    public function createOpenForm()
    {
        $this->form->reset();
    }

    public function componentDellete($id)
    {
        Component::find($id)->delete();
        $this->dispatch('componentGroupUpdateList');
    }

    public function mount()
    {
        //$this->init();
    }

    public function render()
    {
        //dd($this->form);
        
        return view('livewire.configuration.component-modal', ['data' => $this->form->endpoints_arr]);
    }
}
