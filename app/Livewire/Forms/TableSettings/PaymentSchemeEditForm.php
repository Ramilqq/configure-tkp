<?php

namespace App\Livewire\Forms\TableSettings;

use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Tkp\PaymentScheme;

class PaymentSchemeEditForm extends Form
{
    public ?int $id = null;
    public string $name = '';

    // Правила валидации для полей формы
    protected function rules()
    {
        return [
            'id' => 'nullable|exists:payment_schemes,id',
            'name' => 'required|string|min:1|max:255|unique:payment_schemes,name,' . $this->id,
        ];
    }
    // Метод для сохранения изменений в базе данных
    public function saveForm()
    {
        $valideate = $this->validate();
        
        $paymentScheme = PaymentScheme::updateOrCreate(
            ['id' => $this->id],
            ['name' => $valideate['name']]
        );

        $this->fill($paymentScheme);
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
        $paymentScheme = PaymentScheme::find($id);
        
        $this->fill($paymentScheme);
    }
}
