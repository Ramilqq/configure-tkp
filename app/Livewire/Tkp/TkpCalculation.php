<?php

namespace App\Livewire\Tkp;

use App\Livewire\Forms\Tkp\TkpCalculationForm;
use App\Models\Configuration\Configuration;
use App\Models\Tkp\Tkp;
use App\Services\BankRequest;
use Livewire\Component;

class TkpCalculation extends Component
{
    protected $listeners = [
        'addProductUpdateList' => 'mount',
    ];
    
    public TkpCalculationForm $form;
    public int $tkp_version;
    public int $id;
    public array $saved_schema = [];
    public array $pay_params = [];
    public array $banks;


    public function updated($key, $val)
    {
        //dd($key, $val);
        dd($this->form);
        if ($key == 'pay_params.currency') $this->currency($val);
    }

    public function saveParams()
    {
        //dd($this->form);
        $this->form->saveForm($this->id, $this->tkp_version);
    }

    public function mount($id = null, $tkp_version = null)
    {
        $this->tkp_version ?: $this->tkp_version = $tkp_version;
        $this->id ?: $this->id = $id;

        $this->form->editForm($this->id, $this->tkp_version);
        
        $this->form->route = 'tkp.calculation.edit';

        if($configuration = Configuration::where('tkp_version', $this->tkp_version)->first())
        {
            $this->saved_schema = $configuration->toArray()['saved_schema'];
        }

        if($tkp = Tkp::where('id', $this->id)->first())
        {
            $this->pay_params = $tkp->toArray()['pay_params'];
        }

        $banks = new BankRequest();
        $this->banks = $banks->get()['Valute'];

        //$this->currency();

        //dd($this->saved_schema, $this->id, $this->tkp_version);
        //dd($this->pay_params);
    }

    // записываем курс по изменению валюты
    public function currency($val = null)
    {
        
        if ($val === null) $val = $this->pay_params['currency'];
        foreach($this->banks as $bank){ $bank['CharCode'] != $val ?: $this->pay_params['currency_val'] = $bank['Value']; }
    }

    public function render()
    {
        return view('livewire.tkp.tkp-calculation');
    }
}
