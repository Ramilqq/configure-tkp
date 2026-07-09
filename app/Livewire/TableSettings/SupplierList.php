<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\Supplier;

class SupplierList extends BaseCrudList
{
    protected function modelClass(): string
    {
        return Supplier::class;
    }

    protected function viewName(): string
    {
        return 'livewire.table-settings.supplier-list';
    }

    protected function viewVariable(): string
    {
        return 'suppliers';
    }

    protected function updateEvent(): string
    {
        return 'supplierUpdateList';
    }
}
