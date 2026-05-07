<?php

namespace App\Livewire\Forms\Tkp;

use App\Models\Tkp\Tkp;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Facades\Auth;

class TkpContactForm extends Form
{
    public int $id = 0;
    public int $tkp_version = 0;
    public int $user_id;
    public int $update_user_id;

    public string $project_name = '';
    public string $client_name = '';
    public string $contract_owner = 'ООО "Завод РУ-Драйв"';
    public string $implementation_object = '';
    public string $industry = '';

    protected function rules()
    {
        return [
            'tkp_version' => 'nullable',
            'user_id' => 'nullable',
            'update_user_id' => 'nullable',

            'project_name' => 'nullable',
            'client_name' => 'nullable',
            'contract_owner' => 'required',
            'implementation_object' => 'nullable',
            'industry' => 'nullable',
        ];
    }

    public function saveForm($id = null, $tkp_version = null)
    {
        $valideate = $this->validate();
        
        $tkp = Tkp::find($id);

        if($tkp) {
            $valideate['update_user_id'] = Auth::id();
            $tkp->update($valideate);
            $tkp->save();
        } else {
            $valideate['tkp_version'] = now()->timestamp;
            $valideate['user_id'] = Auth::id();
            $valideate['update_user_id'] = Auth::id();
            $tkp = Tkp::create($valideate);
        }
        
        return redirect(route('tkp.sheme.edit' , ['tkp_version' => $tkp->tkp_version, 'id'=> $tkp->id]));
    }

    public function editForm($id = null, $tkp_version = null, )
    {
        $tkp = Tkp::find($id) ?: $this;

        $this->fill($tkp);
    }
}
