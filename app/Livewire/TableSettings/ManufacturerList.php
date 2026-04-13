<?php

namespace App\Livewire\TableSettings;

use Livewire\Component;
use App\Models\Tkp\Manufacturer;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class ManufacturerList extends Component
{
    // обновляем список после редактирования
    #[On('manufacturerUpdateList')]
    public function refreshList()
    {
        $this->render();
    }
    // удаляем позицию
    public function delete($id)
    {
        // Проверка прав пользователя
        $manufacturer = Manufacturer::find($id);
        $this->authorize('delete', $manufacturer);
        
        $manufacturer->delete();
    }
    // рендерим список с кэшированием
    public function render()
    {
        // Проверка прав пользователя
        $manufacturer = new Manufacturer;
        $this->authorize('view', $manufacturer);

        $manufacturer = Cache::remember('manufacturer_list', now()->addHours(6), function () {
            return Manufacturer::all()->sortDesc();
        });
        
        return view('livewire.table-settings.manufacturer-list', ['manufacturer' => $manufacturer]);
    }
}
