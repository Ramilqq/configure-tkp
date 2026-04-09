<?php

namespace App\Livewire\Forms\Tkp;

use App\Models\Tkp\Tkp;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Facades\Auth;

class TkpDeliveryForm extends Form
{
    public int $id;
    public int $tkp_version;
    public int $user_id;
    public int $update_user_id;

    public array $delivery_params = [
        'delivery_time' => 60,
        'delivery_location' => '',
        'payment_scheme' => '',
        'offer_is_valid' => 30,
    ];

    protected function rules()
    {
        return [
            'delivery_params.delivery_time' => 'required',
            'delivery_params.delivery_location' => 'required',
            'delivery_params.payment_scheme' => 'required',
            'delivery_params.offer_is_valid' => 'required',
        ];
    }

    public function saveForm($id = null, $tkp_version = null)
    {
        $valideate = $this->validate();
        
        $tkp = Tkp::find($id);

        if($tkp) {
            $tkp->update_user_id = Auth::id();
            $tkp->delivery_params = $valideate['delivery_params'];
            $tkp->save();
        } else {
            $valideate['tkp_version'] = now()->timestamp;
            $valideate['user_id'] = Auth::id();
            $valideate['update_user_id'] = Auth::id();
            $tkp = Tkp::create($valideate);
        }

        return redirect(route('tkp.calculation.edit' , ['tkp_version' => $tkp->tkp_version, 'id'=> $tkp->id]));
    }

    public function editForm($id = null, $tkp_version = null)
    {
        $tkp = Tkp::find($id) ?: $this;
        
        $this->fill($tkp);
    }
}
