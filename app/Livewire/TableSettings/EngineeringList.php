<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\Engineering;

class EngineeringList extends BaseCrudList
{
    protected function modelClass(): string
    {
        return Engineering::class;
    }

    protected function viewName(): string
    {
        return 'livewire.table-settings.engineering-list';
    }

    protected function viewVariable(): string
    {
        return 'engineering';
    }

    protected function updateEvent(): string
    {
        return 'engineeringUpdateList';
    }
}
