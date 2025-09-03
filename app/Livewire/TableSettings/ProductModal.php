<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\ProductForm;
use App\Models\TableSettings\Template;
use Livewire\Component;

class ProductModal extends Component
{
    protected $listeners = [
        'productEditOpenForm' => 'productEditOpenForm',
        'productCreateOpenForm' => 'productCreateOpenForm',
        'productOpenList' => 'productOpenList',
    ];

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

    public function productOpenList($template_id = 1)
    {
        $this->form->template_id = $template_id;
    }

    public function productCreateOpenForm()
    {
        $this->form->reset();
    }

    public function mount()
    {
        $this->form->manufacturer_id = 0;
        $this->form->currency = 'RUB';
    }

    public function render()
    {   
        $template = Template::all();

        return view('livewire.table-settings.product-modal', ['template' => $template]);
    }
}
