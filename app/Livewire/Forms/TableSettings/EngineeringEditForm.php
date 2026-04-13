<?php

namespace App\Livewire\Forms\TableSettings;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Tkp\Engineering;

class EngineeringEditForm extends Form
{
    public ?int $id = null;
    public string $name = '';
    public string $key = '';
    public string $price = '';

    // Правила валидации для полей формы
    protected function rules()
    {
        return [
            'id' => 'nullable|exists:engineerings,id',
            'name' => 'required|string|min:1|max:255|unique:engineerings,name,' . $this->id,
            'key' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:1|max:999999',
        ];
    }
    // Метод для сохранения изменений в базе данных
    public function saveForm()
    {
        $valideate = $this->validate();
        
        $engineering = Engineering::updateOrCreate(
            ['id' => $this->id],
            ['name' => $valideate['name'],
             'key' => $valideate['key'],
             'price' => $valideate['price']]
        );

        $this->fill($engineering);
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
        $engineering = Engineering::find($id);

        $this->fill($engineering);
    }
}
