<?php

namespace App\Livewire\TableSettings;

use App\Models\User;

class UserList extends BaseCrudList
{
    protected function modelClass(): string
    {
        return User::class;
    }

    protected function viewName(): string
    {
        return 'livewire.table-settings.user-list';
    }

    protected function viewVariable(): string
    {
        return 'user';
    }

    protected function updateEvent(): string
    {
        return 'userUpdateList';
    }
}
