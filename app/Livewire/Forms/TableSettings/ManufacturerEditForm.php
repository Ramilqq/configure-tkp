<?php

namespace App\Livewire\Forms\TableSettings;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Tkp\Manufacturer;

class ManufacturerEditForm extends Form
{
    public ?int $id = null;
    public string $name = '';

    // Правила валидации для полей формы
    protected function rules()
    {
        return [
            'id' => 'nullable|exists:manufacturers,id',
            'name' => 'required|string|min:1|max:255|unique:manufacturers,name,' . $this->id,
        ];
    }
    // Метод для сохранения изменений в базе данных
    public function saveForm()
    {
        $valideate = $this->validate();
        
        $manufacturer = Manufacturer::updateOrCreate(
            ['id' => $this->id],
            ['name' => $valideate['name']]
        );

        $this->fill($manufacturer);
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
        $manufacturer = Manufacturer::find($id);

        $this->fill($manufacturer);
    }
}
