<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\ContractOwner;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class ContractOwnerList extends Component
{
    // обновляем список после редактирования
    #[On('contactOwnerUpdateList')]
    public function refreshList()
    {
        $this->render();
    }
    // удаляем позицию
    public function delete($id)
    {
        // Проверка прав пользователя
        $contactOwner = ContractOwner::find($id);
        $this->authorize('delete', $contactOwner);
        
        $contactOwner->delete();
    }
    // рендерим список с кэшированием
    public function render()
    {
        // Проверка прав пользователя
        $contactOwner = new ContractOwner;
        $this->authorize('view', $contactOwner);

        $contactOwner = Cache::remember('contract_owner_list', now()->addHours(6), function () {
            return ContractOwner::all()->sortDesc();
        });

        return view('livewire.table-settings.contract-owner-list', ['contactOwner' => $contactOwner]);
    }
}
