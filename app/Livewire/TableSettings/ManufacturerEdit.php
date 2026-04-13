<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\ManufacturerEditForm;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Tkp\Manufacturer;

class ManufacturerEdit extends Component
{
    // параметр для формы редактирования
    public ManufacturerEditForm $form;
    // Инициализация формы
    #[On('manufacturerCreateOpen')]
    public function createForm()
    {
        $this->form->createForm();
    }
    // Инициализация формы
    #[On('manufacturerEditOpen')]
    public function editForm($id)
    {
        $this->form->editForm($id);
    }
    // Сохранение изменений
    public function save()
    {
        // Проверка прав пользователя
        $manufacturer = Manufacturer::find($this->form->id);
        $this->authorize('update', $manufacturer);

        // Проверка прав пользователя
        $manufacturer = new Manufacturer;
        $this->authorize('create', $manufacturer);

        $this->form->saveForm($this->form->id);
        $this->dispatch('manufacturerUpdateList')->to('table-settings.manufacturer-list');
    }
    // Рендеринг компонента с данными
    public function render()
    {
        return view('livewire.table-settings.manufacturer-edit');
    }
}
