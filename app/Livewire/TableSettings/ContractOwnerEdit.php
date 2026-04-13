<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\ContractOwnerEditForm;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Tkp\ContractOwner;

class ContractOwnerEdit extends Component
{
    // параметр для формы редактирования
    public ContractOwnerEditForm $form;
    // Инициализация формы
    #[On('contactOwnerCreateOpen')]
    public function createForm()
    {
        $this->form->createForm();
    }
    // Инициализация формы
    #[On('contactOwnerEditOpen')]
    public function editForm($id)
    {
        $this->form->editForm($id);
    }
    // Сохранение изменений
    public function save()
    {
        // Проверка прав пользователя
        if ($this->form->id){
            $contactOwner = ContractOwner::find($this->form->id);
            $this->authorize('update', $contactOwner);
        } else {
            $contactOwner = new ContractOwner;
            $this->authorize('create', $contactOwner);
        }
        
        $this->form->saveForm($this->form->id);
        $this->dispatch('contactOwnerUpdateList')->to('table-settings.contract-owner-list');
    }
    // Рендеринг компонента с данными
    public function render()
    {
        return view('livewire.table-settings.contract-owner-edit');
    }
}
