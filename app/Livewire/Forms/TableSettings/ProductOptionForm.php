<?php

namespace App\Livewire\Forms\TableSettings;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductOptionForm extends Form
{
    public int $template_option_id = 0;
    public int $product_id = 0;
    public $value = '';

    protected function rules()
    {
        return [
            'template_option_id' => 'required|numeric|exists:template_options,id',
            'product_id' => 'required|numeric|exists:products,id',
            'value' => 'nullable|max:30',
        ];
    }
}
