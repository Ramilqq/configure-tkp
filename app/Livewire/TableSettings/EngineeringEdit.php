<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\EngineeringEditForm;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Tkp\Engineering;

class EngineeringEdit extends Component
{
    // параметр для формы редактирования
    public EngineeringEditForm $form;
    // Инициализация формы
    #[On('engineeringCreateOpen')]
    public function createForm()
    {
        $this->form->createForm();
    }
    // Инициализация формы
    #[On('engineeringEditOpen')]
    public function editForm($id)
    {
        $this->form->editForm($id);
    }
    // Сохранение изменений
    public function save()
    {
        // Проверка прав пользователя
        if ($this->form->id){
            $engineering = Engineering::find($this->form->id);
            $this->authorize('update', $engineering);
        } else {
            $engineering = new Engineering;
            $this->authorize('create', $engineering);
        }

        $this->form->saveForm($this->form->id);
        $this->dispatch('engineeringUpdateList')->to('table-settings.engineering-list');
    }
    // Рендеринг компонента с данными
    public function render()
    {
        return view('livewire.table-settings.engineering-edit');
    }
}
