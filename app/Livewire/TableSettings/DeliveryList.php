<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\Delivery;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class DeliveryList extends Component
{
    // обновляем список после редактирования
    #[On('deliveryUpdateList')]
    public function refreshList()
    {
        $this->render();
    }
    // удаляем позицию
    public function delete($id)
    {
        // Проверка прав пользователя
        $delivery = Delivery::find($id);
        $this->authorize('delete', $delivery);

        $delivery->delete();
    }
    // рендерим список с кэшированием
    public function render()
    {
        // Проверка прав пользователя
        $delivery = new Delivery;
        $this->authorize('view', $delivery);

        $delivery = Cache::remember('delivery_list', now()->addHours(6), function () {
            return Delivery::all()->sortDesc();
        });

        return view('livewire.table-settings.delivery-list', ['delivery' => $delivery]);
    }
}
