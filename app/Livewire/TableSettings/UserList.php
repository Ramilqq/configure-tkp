<?php

namespace App\Livewire\TableSettings;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class UserList extends Component
{
    // обновляем список после редактирования
    #[On('userUpdateList')]
    public function refreshList()
    {
        $this->render();
    }
    // удаляем позицию
    public function delete($id)
    {
        // Проверка прав пользователя
        $user = User::find($id);
        $this->authorize('delete', $user);
        
        $user->delete();
    }
    // рендерим список с кэшированием
    public function render()
    {
        // Проверка прав пользователя
        $user = new User;
        $this->authorize('view', $user);

        $user = Cache::remember('user_list', now()->addHours(6), function () {
            return User::all()->sortDesc();
        });

        return view('livewire.table-settings.user-list', ['user' => $user]);
    }
}
