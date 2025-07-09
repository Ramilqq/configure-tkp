<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\TableSettings\Currency;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CurrencyForm extends Form
{
    public int $id = 0;
    public int $template_id = 0;
    public string $key = '';
    public string $name = '';
    public string $calc = '';

    protected function rules()
    {
        return [
            'template_id' => 'required|numeric|exists:templates,id',
            'key' => 'required|min:3|max:20',
            'name' => 'required|min:3|max:200',
            'calc' => 'required|integer|max:200',
        ];
    }


    public function saveForm($id = null)
    {
        $valideate = $this->validate();

        $product = Currency::find($this->id);

        if($product)
        {
            $product->update($valideate);
            $product->save();
        }
        else
        {
            $product = Currency::create($valideate);
        }
        //$this->reset();
        return $product;
    }

    public function editForm($template_id)
    {
        //$product = Currency::find($temlate_id);
        $product = Currency::where(['template_id' => $template_id])->first();
        $this->fill($product);
    }



}
