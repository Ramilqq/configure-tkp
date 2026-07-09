<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\Manufacturer;

class ManufacturerList extends BaseCrudList
{
    protected function modelClass(): string
    {
        return Manufacturer::class;
    }

    protected function viewName(): string
    {
        return 'livewire.table-settings.manufacturer-list';
    }

    protected function viewVariable(): string
    {
        return 'manufacturer';
    }

    protected function updateEvent(): string
    {
        return 'manufacturerUpdateList';
    }
}
