<?php

namespace App\Livewire\Forms\Tkp;

use App\Models\Tkp\Manufacturer;
use Livewire\Form;

class ManufacturerEditForm extends Form
{
    public int $id;
    public string $name;

    // Правила валидации для полей формы
    protected function rules()
    {
        return [
            'id' => 'required|exists:engineerings,id',
            'name' => 'required|string|min:1|max:255',
        ];
    }
    // Метод для сохранения изменений в базе данных
    public function saveForm($id = null)
    {
        $valideate = $this->validate();
        
        $engineering = Manufacturer::find($this->id);
        $engineering->update($valideate);
    }
    // Метод для заполнения формы данными из базы данных
    public function editForm($id = null)
    {
        $engineering = Manufacturer::find($id);

        $this->fill($engineering);
    }
}
