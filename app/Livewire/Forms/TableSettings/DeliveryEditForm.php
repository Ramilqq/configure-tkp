<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\Tkp\Delivery;
use Livewire\Attributes\Validate;
use Livewire\Form;

class DeliveryEditForm extends Form
{
    public ?int $id = null;
    public string $name = '';

    // Правила валидации для полей формы
    protected function rules()
    {
        return [
            'id' => 'nullable|exists:deliveries,id',
            'name' => 'required|string|min:1|max:255|unique:deliveries,name,' . $this->id,
        ];
    }
    // Метод для сохранения изменений в базе данных
    public function saveForm()
    {
        $valideate = $this->validate();
        
        $delivery = Delivery::updateOrCreate(
            ['id' => $this->id],
            ['name' => $valideate['name']]
        );

        $this->fill($delivery);
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
        $delivery = Delivery::find($id);

        $this->fill($delivery);
    }
}
