<?php

namespace App\Livewire\Forms\Tkp;

use App\Models\Tkp\Tkp;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TkpCalculationForm extends Form
{
    public string $route = '';

    public int $id;
    public int $tkp_version;
    public int $user_id;
    public int $update_user_id;

    public string $project_name;
    public string $client_name;
    public string $contract_owner;
    public string $implementation_object;
    public string $industry;

    public array $delivery_params = [];
    
    public array $pay_params = [];

    public string $comments;

    protected function rules()
    {
        return [
            'route' => 'nullable|string',
            'tkp_version' => 'nullable',
            'user_id' => 'nullable',
            'update_user_id' => 'nullable',

            'project_name' => 'required',
            'client_name' => 'required',
            'contract_owner' => 'required',
            'implementation_object' => 'required',
            'industry' => 'required',

            'delivery_params' => 'nullable',
            /*'delivery_time' => 'nullable',
            'delivery_location' => 'nullable',
            'payment_scheme' => 'nullable',
            'offer_is_valid' => 'nullable',*/

            'pay_params' => 'nullable',
        ];
    }

    public function saveForm($id = null, $tkp_version = null)
    {

        $routeName = Route::currentRouteName();

        $valideate = $this->validate();

        $tkp = Tkp::find($id);

        if($tkp)
        {
            $valideate['update_user_id'] = 1;
            $tkp->update($valideate);
            $tkp->save();
        }
        else
        {
            $valideate['tkp_version'] = now()->timestamp;
            $valideate['user_id'] = 1;
            $valideate['update_user_id'] = 0;
            $tkp = Tkp::create($valideate);
        }

        if ($routeName === 'tkp.calculation.edit') return $tkp;
        return redirect(route($valideate['route'] , ['tkp_version' => $tkp->tkp_version, 'id'=> $tkp->id]));
    }

    public function editForm($id = null, $tkp_version = null)
    {
        //dd($tkp_version);
        $tkp = Tkp::find($id) ?: $this;

        /*$tkp->project_name ?:           $tkp->project_name =            $this->project_name;
        $tkp->client_name ?:            $tkp->client_name =             $this->client_name;
        $tkp->contract_owner ?:         $tkp->contract_owner =          $this->contract_owner;
        $tkp->implementation_object ?:  $tkp->implementation_object  =  $this->implementation_object;
        $tkp->industry ?:               $tkp->industry  =               $this->industry;

        $tkp->delivery_time ?:          $tkp->delivery_time =           $this->delivery_time;
        $tkp->delivery_location ?:      $tkp->delivery_location =       $this->delivery_location;
        $tkp->payment_scheme ?:         $tkp->payment_scheme =          $this->payment_scheme;
        $tkp->offer_is_valid ?:         $tkp->offer_is_valid  =         $this->offer_is_valid;

        $tkp->currency ?:               $tkp->currency =                $this->currency;
        $tkp->currency_val ?:           $tkp->currency_val =            $this->currency_val;
        $tkp->bank_loss ?:              $tkp->bank_loss =               $this->bank_loss;
        $tkp->garant_fond ?:            $tkp->garant_fond  =            $this->garant_fond;
        $tkp->bonuse ?:                 $tkp->bonuse  =                 $this->bonuse;
        $tkp->nds ?:                    $tkp->nds  =                    $this->nds;
        $tkp->stab_fond ?:              $tkp->stab_fond  =              $this->stab_fond;
        $tkp->pay_params ?:             $tkp->pay_params  =             $this->pay_params;*/

        
        $this->fill($tkp);

        //dd($tkp, $this->toArray(), $tkp->configuration()->toArray());
    }
}
