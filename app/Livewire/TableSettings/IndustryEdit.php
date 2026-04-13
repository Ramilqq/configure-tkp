<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\IndustryEditForm;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Tkp\Industry;

class IndustryEdit extends Component
{
    // параметр для формы редактирования
    public IndustryEditForm $form;
    // Инициализация формы
    #[On('industryCreateOpen')]
    public function createForm()
    {
        $this->form->createForm();
    }
    // Инициализация формы
    #[On('industryEditOpen')]
    public function editForm($id)
    {
        $this->form->editForm($id);
    }
    // Сохранение изменений
    public function save()
    {
        // Проверка прав пользователя
        $industry = Industry::find($this->form->id);
        $this->authorize('update', $industry);

        // Проверка прав пользователя
        $industry = new Industry;
        $this->authorize('create', $industry);

        $this->form->saveForm($this->form->id);
        $this->dispatch('industryUpdateList')->to('table-settings.industry-list');
    }
    // Рендеринг компонента с данными
    public function render()
    {
        return view('livewire.table-settings.industry-edit');
    }
}
