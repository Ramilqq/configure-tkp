<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\PaymentSchemeEditForm;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Tkp\PaymentScheme;

class PaymentSchemeEdit extends Component
{
    // параметр для формы редактирования
    public PaymentSchemeEditForm $form;
    // обновляем список после редактирования
    #[On('paymentSchemeCreateOpen')]
    public function createForm()
    {
        $this->form->createForm();
    }
    // Инициализация формы
    #[On('paymentSchemeEditOpen')]
    public function editForm($id)
    {
        $this->form->editForm($id);
    }
    // Сохранение изменений
    public function save()
    {
        // Проверка прав пользователя
        $paymentScheme = PaymentScheme::find($this->form->id);
        $this->authorize('update', $paymentScheme);

        // Проверка прав пользователя
        $paymentScheme = new PaymentScheme;
        $this->authorize('create', $paymentScheme);

        $this->form->saveForm();
        $this->dispatch('paymentSchemeUpdateList')->to('table-settings.payment-scheme-list');
    }
    // Рендеринг компонента с данными
    public function render()
    {
        return view('livewire.table-settings.payment-scheme-edit');
    }
}
