<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\Engineering;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class EngineeringList extends Component
{
    // обновляем список после редактирования
    #[On('engineeringUpdateList')]
    public function refreshList()
    {
        $this->render();
    }
    // удаляем позицию
    public function delete($id)
    {
        // Проверка прав пользователя
        $engineering = Engineering::find($id);
        $this->authorize('delete', $engineering);
        
        $engineering->delete();
    }
    // рендерим список с кэшированием
    public function render()
    {
        // Проверка прав пользователя
        $engineering = new Engineering;
        $this->authorize('view', $engineering);

        $engineering = Cache::remember('engineering_list', now()->addHours(6), function () {
            return Engineering::all()->sortDesc();
        });

        return view('livewire.table-settings.engineering-list', ['engineering' => $engineering]);
    }
}
