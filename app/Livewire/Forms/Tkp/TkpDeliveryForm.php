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
    //public string $project_name;
    //public string $client_name;
    //public string $contract_owner;
    //public string $implementation_object;
    //public string $industry;
    //public string $scheme;
    public int $delivery_time = 60;
    public string $delivery_location = '';
    public string $payment_scheme = '';
    public int $offer_is_valid = 30;
    //public string $currency;
    //public string $currency_val;
    //public string $bank_loss;
    //public string $garant_fond;
    //public string $bonuse;
    //public string $nds;
    //public string $stab_fond;
    //public array  $products;
    //public string $version;

    protected function rules()
    {
        return [
            'tkp_version' => 'nullable',
            'user_id' => 'nullable',

            'update_user_id' => 'nullable',
            //'project_name' => 'nullable',
            //'client_name' => 'nullable',
            //'contract_owner' => 'nullable',
            //'implementation_object' => 'nullable',
            //'industry' => 'nullable',

            //'scheme' => 'nullable',

            'delivery_time' => 'required',
            'delivery_location' => 'required',
            'payment_scheme' => 'required',
            'offer_is_valid' => 'required',

            //'currency' => 'nullable',
            //'currency_val' => 'nullable',
            //'bank_loss' => 'nullable',
            //'garant_fond' => 'nullable',
            //'bonuse' => 'nullable',
            //'nds' => 'nullable',
            //'stab_fond' => 'nullable',
            //'products' => 'nullable',
            //'version' => 'nullable',
        ];
    }

    public function saveForm($id = null, $tkp_version = null)
    {
        $valideate = $this->validate();

        $tkp = Tkp::find($id);

        if($tkp)
        {
            $valideate['update_user_id'] = Auth::id();
            $tkp->update($valideate);
            $tkp->save();
        }
        else
        {
            $valideate['tkp_version'] = now()->timestamp;
            $valideate['user_id'] = Auth::id();
            $tkp = Tkp::create($valideate);
        }

        return redirect(route('tkp.calculation.edit' , ['tkp_version' => $tkp->tkp_version, 'id'=> $tkp->id]));
        return $tkp;
    }

    public function editForm($id = null, $tkp_version = null)
    {
        $tkp = Tkp::find($id);

        $tkp->delivery_time ?:      $tkp->delivery_time =       $this->delivery_time;
        $tkp->delivery_location ?:  $tkp->delivery_location =   $this->delivery_location;
        $tkp->payment_scheme ?:     $tkp->payment_scheme =      $this->payment_scheme;
        $tkp->offer_is_valid ?:     $tkp->offer_is_valid  =     $this->offer_is_valid;
        
        $this->fill($tkp);
    }
}
