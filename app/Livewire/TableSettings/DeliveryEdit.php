<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\DeliveryEditForm;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Tkp\Delivery;

class DeliveryEdit extends Component
{
    // параметр для формы редактирования
    public DeliveryEditForm $form;
    // Инициализация формы
    #[On('deliveryCreateOpen')]
    public function createForm()
    {
        $this->form->createForm();
    }
    // Инициализация формы
    #[On('deliveryEditOpen')]
    public function editForm($id)
    {
        $this->form->editForm($id);
    }
    // Сохранение изменений
    public function save()
    {
        // Проверка прав пользователя
        if ($this->form->id){
            $delivery = Delivery::find($this->form->id);
            $this->authorize('update', $delivery);
        } else {
            $delivery = new Delivery;
            $this->authorize('create', $delivery);
        }

        $this->form->saveForm($this->form->id);
        $this->dispatch('deliveryUpdateList')->to('table-settings.delivery-list');
    }
    // Рендеринг компонента с данными
    public function render()
    {
        return view('livewire.table-settings.delivery-edit');
    }
}
