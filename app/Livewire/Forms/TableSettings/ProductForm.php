<?php

namespace App\Livewire\Forms\TableSettings;

use App\Models\TableSettings\Product;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    public string $id = '';
    public string $template_id = '';
    public string $name = '';
    public string $description = '';
    public string $currency = '';
    public float $price = 0.0;
    public float $delivery = 0.0;
    public array $engineering = [];

    protected function rules()
    {
        return [
            'template_id' => 'required|numeric|exists:templates,id',
            'name' => 'required|min:3|max:100|unique:products,name,'.$this->id,
            'description' => 'required|min:3|max:1500',
            'currency' => 'required|min:3|max:3',
            'price' => 'required|numeric|min:0|max:200000',
            'delivery' => 'required|numeric|min:0|max:200000',
            'engineering.*' => 'required|integer|max:200',
            //'engineering.kd' => 'required|integer|max:200',
            //'engineering.pir' => 'required|integer|max:200',
            //'engineering.pnr_po' => 'required|integer|max:200',
            //'engineering.pnr' => 'required|integer|max:200',
            //'engineering.smr_shmr' => 'required|integer|max:200',
            //'engineering.assembly' => 'required|integer|max:200',
            //'engineering.mounting' => 'required|integer|max:200',
        ];
    }

    public function saveForm($id = null)
    {
        $valideate = $this->validate();

        $product = Product::find($this->id);

        if($product)
        {
            $product->update($valideate);
            $product->save();
        }
        else
        {
            $product = Product::create($valideate);
        }
        //$this->reset();
        return $product;
    }

    public function editForm($id)
    {
        $product = Product::find($id);
        $this->fill($product);
    }
}
