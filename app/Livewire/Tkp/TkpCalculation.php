<?php

namespace App\Livewire\Tkp;

use App\Livewire\Forms\Tkp\TkpCalculationForm;
use App\Models\Configuration\Configuration;
use App\Models\Tkp\Engineering;
use App\Models\Tkp\Tkp;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use PSpell\Config;
use Illuminate\Support\Facades\Auth;

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
    public string $dublicate_comments = '';
    public array $table_fields = [
        '9' => 'text',
        '7' => 'discount',
        '10' => 'sel_price_coef',
        '12' => 'gen_contract_service',
        '13' => 'costs_credit',
        '16' => 'risk_reserve',
        '20' => 'tzr_sel',
        '34' => 'sub_work',
        '35' => 'sub_item_price',
        '38' => 'tzr_delivery',
        '39' => 'biz_trips',
        '40' => 'connection',
    ];

    public function tableUpdate($value, $hash, $col_id)
    {
        $change = false;
        foreach($this->saved_schema as $key => &$products){
            if ($key == 'nodes' || $key == 'other'){
                foreach($products as &$product){
                    if($product['product']['hash'] == $hash){
                        $change = true;
                        $product['product'][$this->table_fields[$col_id]] = $value;
                    }
                }
            }
            if ($key == 'connections'){
                foreach($products as &$product){
                    if($product['params']['product']['hash'] == $hash){
                        $change = true;
                        $product['params']['product'][$this->table_fields[$col_id]] = $value;
                    }
                }
            }
        }
        //dd($this->saved_schema);
        if ($change)  $this->saveSheam();
    }

    public function saveSheam()
    {
        $configurationModal = Configuration::where('tkp_version', $this->tkp_version)->first();
        $configurationModal->update(['saved_schema' => $this->saved_schema]);
    }

    public function saveParams()
    {
        $tkp = Tkp::findOrFail($this->id);
        $this->authorize('update', $tkp);

        $this->form->saveForm($this->id, $this->tkp_version);
    }

    public function saveConfiguration ()
    {
        $tkp = Tkp::findOrFail($this->id);
        $this->authorize('update', $tkp);
        
        if($configuration = Configuration::where('tkp_version', $this->tkp_version)->first())
        {
            $configuration->update(['saved_schema' => $this->saved_schema]);
        }
    }

    public function saveDublicate()
    {
        if ($this->dublicate_comments == '') {
            $this->addError('dublicate_comments', 'Комментарий к новой версии не может быть пустым.');
            return;
        }

        $tkpModel = Tkp::findOrFail($this->id);
        $configurationModel = $tkpModel->configuration()->first();

        $tkpModel->comments = $this->dublicate_comments;
        $tkpModel->tkp_version = now()->timestamp;

        $tkpModel->user_id = Auth::id();
        $tkpModel->update_user_id = Auth::id();

        $configurationModel->tkp_version = $tkpModel->tkp_version;

        $newTkp = Tkp::create($tkpModel->toArray());
        $newConfiguration = Configuration::create($configurationModel->toArray());
        
        if (!$newTkp || !$newConfiguration) {
            $this->addError('dublicate_comments', 'Ошибка при создании копии. Пожалуйста, попробуйте снова.');
            return;
        }

        redirect()->route('tkp.calculation.edit', ['id' => $newTkp->id, 'tkp_version' => $newTkp->tkp_version]);
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
            $this->saved_schema['engineering'] = $engineering->pluck('price', 'key')->toArray();

            $this->saveConfiguration();
        }

        $banks = app(\App\Services\BankRequest::class);
        
        $this->banks = $banks->get()['Valute'] ?? [];
        
        $this->form->saveForm($this->id, $this->tkp_version);
    }


    // записываем обновление цен по всем продуктам
    public function currency()
    {
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

    public function openPdf()
    {
        if($tkp = Tkp::where('id', $this->id)->first()) {
            if($tkp->pay_params['resault_total'] == 0) {
                $this->saveParams();
            }
        }

        redirect(route('tkp.pdf.show', ['id' => $this->id, 'tkp_version' => $this->tkp_version]));
    }

    public function render()
    {
        return view('livewire.tkp.tkp-calculation');
    }
}
