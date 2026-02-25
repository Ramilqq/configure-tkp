<?php

namespace App\Livewire\Tkp;

use App\Livewire\Forms\Tkp\ManufacturerEditForm;
use Livewire\Component;
use Livewire\Attributes\On;

class ManufacturerEdit extends Component
{
    // параметр для формы редактирования
    public ManufacturerEditForm $form;
    // Инициализация формы
    #[On('fillManufacturerEdit')]
    public function editForm($id)
    {
        $this->form->editForm($id);
    }
    // Сохранение изменений
    public function save()
    {
        $this->form->saveForm($this->form->id);
        $this->dispatch('manufacturerUpdated')->to('tkp.manufacturer-list');
    }
    // Рендеринг компонента с данными
    public function render()
    {       
        return view('livewire.tkp.manufacturer-edit');
    }
}
