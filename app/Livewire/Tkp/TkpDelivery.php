<?php

namespace App\Livewire\Tkp;

use App\Livewire\Forms\Tkp\TkpDeliveryForm;
use App\Models\Tkp\Delivery;
use App\Models\Tkp\PaymentScheme;
use App\Models\Tkp\Tkp;
use Livewire\Component;

class TkpDelivery extends Component
{
    public TkpDeliveryForm $form;

    public $tkp_version;
    public $id;

    public function saveForm()
    {
        if ($this->id) {
            $tkp = Tkp::findOrFail($this->id);
            $this->authorize('update', $tkp);
        }
        
        $this->form->saveForm($this->id, $this->tkp_version);
    }

    public function mount()
    {
        // Проверка авторизации
        if ($this->id) {
            $tkp = Tkp::findOrFail($this->id);
            $this->authorize('view', $tkp);
        }
        
        // заполнение полей формы
        $this->form->editForm($this->id, $this->tkp_version);
    }

    public function render()
    {
        $deliveres = Delivery::all();
        $payments_scheme = PaymentScheme::all();

        return view('livewire.tkp.tkp-delivery', [
            'deliveres' => $deliveres,
            'payments_scheme' => $payments_scheme,
        ]);
    }
}
