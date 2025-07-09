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
    public int $po = 0;
    public int $kd = 0;
    public int $pir = 0;
    public int $pnr_po = 0;
    public int $pnr = 0;
    public int $smr_shmr = 0;

    protected function rules()
    {
        return [
            'template_id' => 'required|numeric|exists:templates,id',
            'name' => 'required|min:3|max:20',
            'description' => 'required|min:3|max:200',
            'po' => 'required|integer|max:200',
            'kd' => 'required|integer|max:200',
            'pir' => 'required|integer|max:200',
            'pnr_po' => 'required|integer|max:200',
            'pnr' => 'required|integer|max:200',
            'smr_shmr' => 'required|integer|max:200',
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
        $this->reset();
        return $product;
    }

    public function editForm($id)
    {
        $product = Product::find($id);
        $this->fill($product);
    }
}
