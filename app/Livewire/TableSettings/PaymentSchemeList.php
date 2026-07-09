<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\PaymentScheme;

class PaymentSchemeList extends BaseCrudList
{
    protected function modelClass(): string
    {
        return PaymentScheme::class;
    }

    protected function viewName(): string
    {
        return 'livewire.table-settings.payment-scheme-list';
    }

    protected function viewVariable(): string
    {
        return 'paymentScheme';
    }

    protected function updateEvent(): string
    {
        return 'paymentSchemeUpdateList';
    }
}
