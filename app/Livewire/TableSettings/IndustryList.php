<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\Industry;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class IndustryList extends Component
{
    // обновляем список после редактирования
    #[On('industryUpdateList')]
    public function refreshList()
    {
        $this->render();
    }
    // удаляем позицию
    public function delete($id)
    {
        // Проверка прав пользователя
        $industry = Industry::find($id);
        $this->authorize('delete', $industry);
        
        $industry->delete();
    }
    // рендерим список с кэшированием
    public function render()
    {
        // Проверка прав пользователя
        $industry = new Industry;
        $this->authorize('view', $industry);

        $industry = Cache::remember('industry_list', now()->addHours(6), function () {
            return Industry::all()->sortDesc();
        });

        return view('livewire.table-settings.industry-list', ['industry' => $industry]);
    }
}
