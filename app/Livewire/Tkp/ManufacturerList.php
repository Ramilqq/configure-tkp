<?php

namespace App\Livewire\Tkp;

use App\Models\Tkp\Manufacturer;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class ManufacturerList extends Component
{
    // передаем данные в форму редактирования
    #[On('manufacturerEditOpen')]
    public function openForm($id)
    {
        $this->dispatch('fillManufacturerEdit', $id)->to('tkp.manufacturer-edit');
    }
    // обновляем список после редактирования
    #[On('manufacturerUpdated')]
    public function refreshList()
    {
        $this->render();
    }
    // удаляем позицию
    public function delete($id)
    {
        $manufacturer = Manufacturer::findOrFail($id);

        // Проверка авторизации
        $this->authorize('delete', $manufacturer);
        
        $manufacturer->delete();
    }
    // рендерим список с кэшированием
    public function render()
    {
        $manufacturer = Cache::remember('manufacturer_list', now()->addHours(6), function () {
            return Manufacturer::all()->sortDesc();
        });

        return view('livewire.tkp.manufacturer-list', ['manufacturer' => $manufacturer]);
    }
}
