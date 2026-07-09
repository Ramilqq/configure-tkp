<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\ContractOwner;

class ContractOwnerList extends BaseCrudList
{
    protected function modelClass(): string
    {
        return ContractOwner::class;
    }

    protected function viewName(): string
    {
        return 'livewire.table-settings.contract-owner-list';
    }

    protected function viewVariable(): string
    {
        return 'contactOwner';
    }

    protected function updateEvent(): string
    {
        return 'contactOwnerUpdateList';
    }
}
