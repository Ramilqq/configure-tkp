<?php

namespace App\Livewire\Forms\Tkp;

use App\Models\Tkp\Engineering;
use Livewire\Form;

class EngineeringEditForm extends Form
{
    public int $id;
    public string $name;
    public string $key;
    public string $price;

    // Правила валидации для полей формы
    protected function rules()
    {
        return [
            'id' => 'required|exists:engineerings,id',
            'name' => 'required|string|min:1|max:255',
            'key' => 'required|string|min:1|max:255',
            'price' => 'required|numeric|min:1|max:999999',
        ];
    }
    // Метод для сохранения изменений в базе данных
    public function saveForm($id = null)
    {
        $valideate = $this->validate();
        
        $engineering = Engineering::find($this->id);
        $engineering->update($valideate);
    }
    // Метод для заполнения формы данными из базы данных
    public function editForm($id = null)
    {
        $engineering = Engineering::find($id);

        $this->fill($engineering);
    }
}
