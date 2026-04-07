<?php

namespace App\Livewire\Tkp\Modal;

use App\Livewire\Forms\Tkp\AddProductForm;
use Livewire\Component;
use App\Models\Tkp\Tkp;

class AddProduct extends Component
{
    protected $listeners = [
        'addProductOpenForm' => 'addProductOpenForm',
        'editProductOpenForm' => 'editProductOpenForm',
        'addProductRemove' => 'addProductRemove',
    ];

    public AddProductForm $form;
    public int $tkp_version;
    public string $product_id;
    public array $banks;

    public function saveForm()
    {
        $tkp = Tkp::where('tkp_version', $this->tkp_version)->firstOrFail();
        $this->authorize('update', $tkp);

        $this->form->saveForm($this->tkp_version);
        $this->dispatch('addProductUpdateList');

    }

    public function addProductOpenForm($product_id)
    {
        $this->form->openForm($this->tkp_version, $product_id);
    }

    public function editProductOpenForm($product_id)
    {
        $this->form->openForm($this->tkp_version, $product_id);
    }

    public function addProductRemove($product_id)
    {
        $tkp = Tkp::where('tkp_version', $this->tkp_version)->firstOrFail();
        $this->authorize('delete', $tkp);

        $this->form->remove($this->tkp_version, $product_id);
        $this->dispatch('addProductUpdateList');
    }

    public function mount($tkp_version = 0, $banks = [])
    {
        $this->tkp_version = $tkp_version;
        $this->banks = $banks;
    }

    public function render()
    {
        
        return view('livewire.tkp.modal.add-product');
    }
}
