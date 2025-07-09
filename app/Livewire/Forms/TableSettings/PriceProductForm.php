<?php

namespace App\Livewire\Forms\TableSettings;

use Livewire\Attributes\Validate;
use Livewire\Form;

class PriceProductForm extends Form
{
    public string $product_id = '';
    public string $currency_id = '';
    public string $price = '';

    protected function rules()
    {
        return [
            //'product_id' => 'required|numeric|exists:templates,id',
            //'currency_id' => 'required|min:3|max:20',
            'price' => 'required|decimal:2|min:1|max:200000',
        ];
    }


}
