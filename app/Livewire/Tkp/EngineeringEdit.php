<?php

namespace App\Livewire\Tkp;

use App\Livewire\Forms\Tkp\EngineeringEditForm;
use Livewire\Component;
use Livewire\Attributes\On;

class EngineeringEdit extends Component
{
    // параметр для формы редактирования
    public EngineeringEditForm $form;
    // Инициализация формы
    #[On('fillEngineeringEdit')]
    public function editForm($id)
    {
        $this->form->editForm($id);
    }
    // Сохранение изменений
    public function save()
    {
        $this->form->saveForm($this->form->id);
        $this->dispatch('engineeringUpdated')->to('tkp.engineering-list');
    }
    // Рендеринг компонента с данными
    public function render()
    {       
        return view('livewire.tkp.engineering-edit');
    }
}
