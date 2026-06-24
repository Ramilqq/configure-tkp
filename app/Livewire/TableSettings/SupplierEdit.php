<?php

namespace App\Livewire\TableSettings;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Tkp\Supplier;
use App\Livewire\Forms\TableSettings\SupplierEditForm;

class SupplierEdit extends Component
{
    public SupplierEditForm $form;

    #[On('supplierCreateOpen')]
    public function createForm()
    {
        $this->form->createForm();
    }

    #[On('supplierEditOpen')]
    public function editForm($id)
    {
        $this->form->editForm($id);
    }

    public function save()
    {
        if ($this->form->id) {
            $supplier = Supplier::find($this->form->id);
            $this->authorize('update', $supplier);
        } else {
            $supplier = new Supplier;
            $this->authorize('create', $supplier);
        }

        $this->form->saveForm();
        $this->dispatch('supplierUpdateList')->to('table-settings.supplier-list');
    }

    public function render()
    {
        return view('livewire.table-settings.supplier-edit');
    }
}
