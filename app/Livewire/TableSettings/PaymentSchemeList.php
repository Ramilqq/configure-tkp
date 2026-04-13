<?php

namespace App\Livewire\TableSettings;

use App\Models\Tkp\PaymentScheme;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;

class PaymentSchemeList extends Component
{
    // обновляем список после редактирования
    #[On('paymentSchemeUpdateList')]
    public function refreshList()
    {
        $this->render();
    }
    // удаляем позицию
    public function delete($id)
    {
        // Проверка прав пользователя
        $paymentScheme = PaymentScheme::find($id);
        $this->authorize('delete', $paymentScheme);
        
        $paymentScheme->delete();
    }
    // рендерим список с кэшированием
    public function render()
    {
        // Проверка прав пользователя
        $paymentScheme = new PaymentScheme;
        $this->authorize('view', $paymentScheme);

        $paymentScheme = Cache::remember('payment_scheme_list', now()->addHours(6), function () {
            return PaymentScheme::all()->sortDesc();
        });

        return view('livewire.table-settings.payment-scheme-list', ['paymentScheme' => $paymentScheme]);
    }
}
