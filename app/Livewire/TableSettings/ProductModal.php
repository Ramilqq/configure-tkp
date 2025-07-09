<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\ProductForm;
use App\Models\TableSettings\Template;
use Livewire\Component;

class ProductModal extends Component
{
    protected $listeners = ['productEditOpenForm' => 'productEditOpenForm', 'productCreateOpenForm' => 'productCreateOpenForm'];

    public ProductForm $form;

    public function saveForm()
    {
        $valideate = $this->form->saveForm();
        $this->dispatch('productUpdateList', template_id: $valideate->template_id);
    }

    public function productEditOpenForm($id = null)
    {
        $this->form->editForm($id);
    }

    public function productCreateOpenForm()
    {
        $this->form->reset();
    }

    public function render()
    {   
        $template = Template::all();

        return view('livewire.table-settings.product-modal', ['template' => $template]);
    }
}
