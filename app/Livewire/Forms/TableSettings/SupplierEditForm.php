<?php

namespace App\Livewire\Forms\TableSettings;

use Livewire\Form;
use App\Models\Tkp\Supplier;

class SupplierEditForm extends Form
{
    public ?int $id = null;
    public string $name = '';

    protected function rules()
    {
        return [
            'id'   => 'nullable|exists:suppliers,id',
            'name' => 'required|string|min:1|max:255|unique:suppliers,name,' . $this->id,
        ];
    }

    public function saveForm()
    {
        $validated = $this->validate();

        $supplier = Supplier::updateOrCreate(
            ['id' => $this->id],
            ['name' => $validated['name']]
        );

        $this->fill($supplier);
    }

    public function createForm()
    {
        $this->reset();
        $this->resetValidation();
    }

    public function editForm($id = null)
    {
        $this->resetValidation();
        $this->fill(Supplier::find($id));
    }
}
