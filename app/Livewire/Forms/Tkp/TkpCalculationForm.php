<?php

namespace App\Livewire\Forms\Tkp;

use App\Models\Tkp\Tkp;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Support\Facades\Auth;

class TkpCalculationForm extends Form
{
    public string $route = '';

    public int $id = 0;
    public int $tkp_version = 0;
    public int $user_id;
    public int $update_user_id;

    public string $project_name;
    public string $client_name;
    public string $contract_owner;
    public string $implementation_object;
    public string $industry;

    public array $delivery_params = [];
    
    public array $pay_params = [
        'marketing' => 0,
        'nds' => 0,
        'reserve' => 0,
        'resault_total' => 0,
        'resault_total_nds' => 0,
        'resault_nds' => 0,
    ];

    public ?string $comments;

    protected function rules()
    {
        return [
            'route' => 'nullable|string',
            'tkp_version' => 'nullable',
            'user_id' => 'nullable',
            'update_user_id' => 'nullable',

            'comments' => 'nullable',

            'pay_params.marketing' => 'numeric',
            'pay_params.nds' => 'nullable',
            'pay_params.reserve' => 'nullable',
            'pay_params.resault_total' => 'nullable',
            'pay_params.resault_total_nds' => 'nullable',
            'pay_params.resault_nds' => 'nullable',
        ];
    }

    public function saveForm($id = null, $tkp_version = null)
    {
        $id ?: $id = $this->id;
        $tkp_version ?: $tkp_version = $this->tkp_version;

        $routeName = Route::currentRouteName();

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
            $valideate['update_user_id'] = Auth::id();
            $tkp = Tkp::create($valideate);
        }
        
        return;
    }

    public function editForm($id = null, $tkp_version = null)
    {
        $tkp = Tkp::find($id) ?: $this;

        $this->fill($tkp);
    }
}
