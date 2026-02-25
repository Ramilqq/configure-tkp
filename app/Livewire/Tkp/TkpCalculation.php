<?php

namespace App\Livewire\Tkp;

use App\Livewire\Forms\Tkp\TkpCalculationForm;
use App\Models\Configuration\Configuration;
use App\Models\Tkp\Engineering;
use App\Models\Tkp\Tkp;
use App\Services\BankRequest;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

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
        $this->form->pay_params = $this->pay_params;
        $this->form->saveForm($this->id);
    }

    public function saveParams()
    {
        $this->form->saveForm($this->id, $this->tkp_version);
        //dd($this->form);
    }

    public function saveConfiguration () {
        if($configuration = Configuration::where('tkp_version', $this->tkp_version)->first())
        {
            $configuration->update(['saved_schema' => $this->saved_schema]);
        }
    }

    public function mount($id = null, $tkp_version = null)
    {
        $this->tkp_version ?: $this->tkp_version = $tkp_version;
        $this->id ?: $this->id = $id;

        // Проверка авторизации
        if ($this->id && $this->tkp_version) {
            $tkp = Tkp::findOrFail($this->id);
            $this->authorize('view', $tkp);
        }

        $this->form->editForm($this->id, $this->tkp_version);
        
        $this->form->route = 'tkp.calculation.edit';

        $configurationModal = Configuration::where('tkp_version', $this->tkp_version)->first();
        $configuration = $configurationModal?->toArray();

        if($configuration)
        {
            $this->saved_schema = $configuration['saved_schema'];
        }

        if($tkp = Tkp::where('id', $this->id)->first())
        {
            $this->pay_params = $tkp->toArray()['pay_params'];
        }

        $engineering = Cache::remember('engineering_list', now()->addHours(6), function () {
            return Engineering::all()->sortDesc();
        });

        if ($engineering && !isset($configuration['saved_schema']['engineering'])) {
            $this->saved_schema['engineering'] = $engineering->pluck('price', 'name')->toArray();

            $this->saveConfiguration();
        }

        $banks = new BankRequest();
        $this->banks = $banks->get()['Valute'];
    }

    // записываем обновление цен по всем продуктам
    public function currency()
    {
        //dd($this->saved_schema);

        foreach (['nodes', 'connections', 'other'] as $name) {
            foreach ($this->saved_schema[$name] as $key => $product) {
                foreach ($this->banks as $banks) {
                    // обновление валюты
                    if ($banks['CharCode'] == $product['product']['currency']) {
                        $this->saved_schema[$name][$key]['product']['currency_val'] = $banks['Value'];
                    }
                    // обновление валюты RUB
                    if ('RUB' == $product['product']['currency']) {
                        $this->saved_schema[$name][$key]['product']['currency_val'] = 1;
                    }
                }
            }
        }
        

        $this->saveConfiguration();
    }

    public function render()
    {
        //dd($this->pay_params);
        return view('livewire.tkp.tkp-calculation');
    }
}
