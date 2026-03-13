<?php

namespace App\Livewire\Forms\Tkp;

use App\Models\Tkp\Tkp;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Facades\Auth;

class TkpContactForm extends Form
{
    public int $id;
    public int $tkp_version;
    public int $user_id;
    public int $update_user_id;

    public string $project_name;
    public string $client_name;
    public string $contract_owner;
    public string $implementation_object;
    public string $industry;

    //public string $scheme;

    //public string $delivery_time;
    //public string $delivery_location;
    //public string $payment_scheme;
    //public string $offer_is_valid;
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

            'project_name' => 'required',
            'client_name' => 'required',
            'contract_owner' => 'required',
            'implementation_object' => 'required',
            'industry' => 'required',

            //'scheme' => 'nullable',

            //'delivery_time' => 'nullable',
            //'delivery_location' => 'nullable',
            //'payment_scheme' => 'nullable',
            //'offer_is_valid' => 'nullable',
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
            $valideate['update_user_id'] = 0;
            $tkp = Tkp::create($valideate);
        }
        return redirect(route('tkp.sheme.edit' , ['tkp_version' => $tkp->tkp_version, 'id'=> $tkp->id]));
        return $tkp;
    }

    public function editForm($id = null, $tkp_version = null, )
    {
        $tkp = Tkp::find($id);

        $tkp->project_name ?:           $tkp->project_name =            $this->project_name;
        $tkp->client_name ?:            $tkp->client_name =             $this->client_name;
        $tkp->contract_owner ?:         $tkp->contract_owner =          $this->contract_owner;
        $tkp->implementation_object ?:  $tkp->implementation_object  =  $this->implementation_object;
        $tkp->industry ?:               $tkp->industry  =               $this->industry;

        $this->fill($tkp);
    }
}
