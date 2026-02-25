<?php

namespace App\Livewire\Tkp;

use App\Models\Tkp\Engineering;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class EngineeringList extends Component
{
    // передаем данные в форму редактирования
    #[On('engineeringEditOpen')]
    public function openForm($id)
    {
        $this->dispatch('fillEngineeringEdit', $id)->to('tkp.engineering-edit');
    }
    // обновляем список после редактирования
    #[On('engineeringUpdated')]
    public function refreshList()
    {
        $this->render();
    }
    // удаляем позицию
    public function delete($id)
    {
        $engineering = Engineering::findOrFail($id);

        // Проверка авторизации
        $this->authorize('delete', $engineering);
        
        $engineering->delete();
    }
    // рендерим список с кэшированием
    public function render()
    {
        $engineering = Cache::remember('engineering_list', now()->addHours(6), function () {
            return Engineering::all()->sortDesc();
        });

        return view('livewire.tkp.engineering-list', ['engineering' => $engineering]);
    }
}
