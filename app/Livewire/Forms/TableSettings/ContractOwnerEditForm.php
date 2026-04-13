<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\Tkp\ContractOwner;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ContractOwnerEditForm extends Form
{
    public ?int $id = null;
    public string $name = '';

    // Правила валидации для полей формы
    protected function rules()
    {
        return [
            'id' => 'nullable|exists:contract_owners,id',
            'name' => 'required|string|min:1|max:255|unique:contract_owners,name,' . $this->id,
        ];
    }
    // Метод для сохранения изменений в базе данных
    public function saveForm()
    {
        $valideate = $this->validate();
        
        $contactOwner = ContractOwner::updateOrCreate(
            ['id' => $this->id],
            ['name' => $valideate['name']]
        );

        $this->fill($contactOwner);
    }
    // Метод для создания новой записи в базе данных
    public function createForm()
    {
        $this->reset();
        $this->resetValidation();
        
        $this->fill($this);
    }
    // Метод для заполнения формы данными из базы данных
    public function editForm($id = null)
    {
        $this->resetValidation();
        $contactOwner = ContractOwner::find($id);

        $this->fill($contactOwner);
    }
}
