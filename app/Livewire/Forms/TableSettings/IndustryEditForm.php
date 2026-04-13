<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\Tkp\Industry;
use Livewire\Attributes\Validate;
use Livewire\Form;

class IndustryEditForm extends Form
{
    public ?int $id = null;
    public string $name = '';

    // Правила валидации для полей формы
    protected function rules()
    {
        return [
            'id' => 'nullable|exists:industries,id',
            'name' => 'required|string|min:1|max:255|unique:industries,name,' . $this->id,
        ];
    }
    // Метод для сохранения изменений в базе данных
    public function saveForm()
    {
        $valideate = $this->validate();
        
        $industry = Industry::updateOrCreate(
            ['id' => $this->id],
            ['name' => $valideate['name']]
        );

        $this->fill($industry);
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
        $industry = Industry::find($id);

        $this->fill($industry);
    }
}
