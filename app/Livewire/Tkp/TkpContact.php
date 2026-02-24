<?php

namespace App\Livewire\Tkp;

use App\Livewire\Forms\Tkp\TkpCalculationForm;
use App\Models\Tkp\ContractOwner;
use App\Models\Tkp\Industry;
use App\Models\Tkp\Tkp;
use Livewire\Component;

class TkpContact extends Component
{
    public TkpCalculationForm $form;

    public $tkp_version;
    public $id;

    public function saveForm()
    {
        $this->form->saveForm();
    }

    public function mount($id = null, $tkp_version = null)
    {
        ($id && $tkp_version) ? $this->form->editForm($id, $tkp_version) : null;
        $this->form->route = 'tkp.sheme.edit';

        // Проверка авторизации
        if ($id && $tkp_version) {
            $tkp = Tkp::findOrFail($id);
            $this->authorize('view', $tkp);
        }
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
