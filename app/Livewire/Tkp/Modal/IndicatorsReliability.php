<?php

namespace App\Livewire\Tkp\Modal;

use App\Livewire\Forms\Tkp\IndicatorsReliabilityForm;
use Livewire\Component;
use Livewire\Attributes\On;

class IndicatorsReliability extends Component
{
    public IndicatorsReliabilityForm $form;

    #[On('editIndicatorsReliabilityOpenForm')]
    public function openModalForm($tkp_version, $hash)
    {
        $this->form->tkp_version    = $tkp_version;
        $this->form->hash           = $hash;

        $this->form->openForm();
    }

    public function saveForm()
    {
        $this->form->saveForm();
    }

    public function render()
    {
        return view('livewire.tkp.modal.indicators-reliability');
    }
}
