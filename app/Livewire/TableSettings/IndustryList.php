<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\Industry;

class IndustryList extends BaseCrudList
{
    protected function modelClass(): string
    {
        return Industry::class;
    }

    protected function viewName(): string
    {
        return 'livewire.table-settings.industry-list';
    }

    protected function viewVariable(): string
    {
        return 'industry';
    }

    protected function updateEvent(): string
    {
        return 'industryUpdateList';
    }
}
