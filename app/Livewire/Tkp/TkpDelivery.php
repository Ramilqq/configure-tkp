<?php

namespace App\Livewire\Tkp;

use App\Livewire\Forms\Tkp\TkpCalculationForm;
use App\Models\Tkp\Delivery;
use App\Models\Tkp\PaymentScheme;
use Livewire\Component;

class TkpDelivery extends Component
{
    public TkpCalculationForm $form;

    public $tkp_version;
    public $id;

    public function saveForm()
    {
        $this->form->saveForm($this->id, $this->tkp_version,);
    }

    public function mount($tkp_version, $id)
    {
        ($id && $tkp_version) ? $this->form->editForm($id, $tkp_version) : null;
        $this->form->route = 'tkp.calculation.edit';    
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
