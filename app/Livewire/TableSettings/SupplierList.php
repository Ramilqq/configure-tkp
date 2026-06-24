<?php

namespace App\Livewire\TableSettings;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Tkp\Supplier;
use Illuminate\Support\Facades\Cache;

class SupplierList extends Component
{
    #[On('supplierUpdateList')]
    public function refreshList()
    {
        $this->render();
    }

    public function delete($id)
    {
        $supplier = Supplier::find($id);
        $this->authorize('delete', $supplier);
        $supplier->delete();
    }

    public function render()
    {
        $supplier = new Supplier;
        $this->authorize('view', $supplier);

        $suppliers = Cache::remember('supplier_list', now()->addHours(6), function () {
            return Supplier::all()->sortDesc();
        });

        return view('livewire.table-settings.supplier-list', ['suppliers' => $suppliers]);
    }
}
