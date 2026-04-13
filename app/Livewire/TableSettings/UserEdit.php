<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\UserEditForm;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class UserEdit extends Component
{
    // параметр для формы редактирования
    public UserEditForm $form;
    // обновляем список после редактирования
    #[On('userCreateOpen')]
    public function createForm()
    {
        $this->form->createForm();
    }
    // Инициализация формы
    #[On('userEditOpen')]
    public function editForm($id)
    {
        $this->form->editForm($id);
    }
    // Сохранение изменений
    public function save()
    {
        // Проверка прав пользователя
        if ($this->form->id){
            $user = User::find($this->form->id);
            $this->authorize('update', $user);
        }else{
            $user = new User;
            $this->authorize('create', $user);
        }       

        $this->form->saveForm();
        $this->dispatch('userUpdateList')->to('table-settings.user-list');
    }
    // Рендеринг компонента с данными
    public function render()
    {
        return view('livewire.table-settings.user-edit');
    }
}
