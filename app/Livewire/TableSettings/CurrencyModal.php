<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\CurrencyForm;
use Livewire\Component;

class CurrencyModal extends Component
{
    protected $listeners = ['currencyEditOpenForm' => 'currencyEditOpenForm', 'currencyCreateOpenForm' => 'currencyCreateOpenForm'];

    public CurrencyForm $form;

    public function saveForm()
    {
        $valideate = $this->form->saveForm();
        $this->dispatch('templateUpdateList');
    }

    public function currencyEditOpenForm($template_id = null)
    {
        $this->form->editForm($template_id);
    }

    public function currencyCreateOpenForm()
    {
        $this->form->reset();
    }

    public function render()
    {
        $xml = simplexml_load_file("http://www.cbr.ru/scripts/XML_daily.asp");
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        //dd($array['Valute']);
        $currency = $array['Valute'];
        return view('livewire.table-settings.currency-modal', ['currency' => $currency]);
    }
}
