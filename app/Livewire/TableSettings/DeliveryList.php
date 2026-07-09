<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\Delivery;

class DeliveryList extends BaseCrudList
{
    protected function modelClass(): string
    {
        return Delivery::class;
    }

    protected function viewName(): string
    {
        return 'livewire.table-settings.delivery-list';
    }

    protected function viewVariable(): string
    {
        return 'delivery';
    }

    protected function updateEvent(): string
    {
        return 'deliveryUpdateList';
    }
}
