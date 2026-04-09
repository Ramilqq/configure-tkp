<?php

namespace App\Livewire\Tkp;

use App\Livewire\Forms\Tkp\TkpContactForm;
use App\Models\Tkp\ContractOwner;
use App\Models\Tkp\Industry;
use App\Models\Tkp\Tkp;
use Livewire\Component;

class TkpContact extends Component
{
    public TkpContactForm $form;

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
        $industes = Industry::all()->toArray();
        $contract_owners = ContractOwner::all()->toArray();

        return view('livewire.tkp.tkp-contact', [
            'industes' => $industes,
            'contract_owners' => $contract_owners,
        ]);
    }
}
