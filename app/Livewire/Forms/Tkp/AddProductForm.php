<?php

namespace App\Livewire\Forms\Tkp;

use App\Models\Configuration\Configuration;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AddProductForm extends Form
{
    public int $tkp_version = 0;
    public int $product_id = 0;
    public array $new_product = [
        'product_name' => '',
        'product' => [
            'id' => 0,
            'template_id' => 0,
            'name',
            'description',
            'manufacturer_id' => 0,
            'currency_val' => 1,
            'currency' => 'RUB',
            'price',
            'delivery',
            'engineering' => [],
            'product_option' => [],
        ],
    ];

    protected function rules()
    {
        return [
            'new_product.product_name' => 'nullable',
            'new_product.product.id' => 'nullable',
            'new_product.product.name' => 'required|min:1|max:100',
            'new_product.product.description' => 'required|min:1|max:250',
            'new_product.product.manufacturer_id' => 'nullable',
            'new_product.product.currency_val' => 'nullable',
            'new_product.product.currency' => 'nullable',
            'new_product.product.price' => 'required',
            'new_product.product.delivery' => 'nullable',
            'new_product.product.engineering' => 'nullable',
            'new_product.product.product_option' => 'nullable',
        ];
    }

    public function saveForm()
    {
        
        $valideate = $this->validate();
        $this->new_product = $valideate['new_product'];
        
        
        $configuration = Configuration::where('tkp_version', $this->tkp_version)->first();

        if(!$configuration) return [];
        
        $this->new_product['product']['id'] ?: $this->new_product['product']['id']  = now()->timestamp;

        $data = $configuration->toArray();
        $other = collect($data['saved_schema']['other']);

        if ($this->product_id == 0) $other->push($this->new_product);
        else $other->transform(function (array $item, int $key) { if ($this->product_id == $item['product']['id']) { return $this->new_product; } return $item; });

        $data['saved_schema']['other'] = $other->toArray();

        $configuration->update($data);
        $configuration->save();
        
        $this->resetExcept(['tkp_version', 'product_id']);
        return $configuration;
    }

    public function openForm($tkp_version, $product_id)
    {
        $this->reset();
        $this->tkp_version = $tkp_version;
        $this->product_id = $product_id;

        $configuration = Configuration::where('tkp_version', $tkp_version)->first();

        $filter['new_product'] = collect($configuration->saved_schema['other'])->first(function (array $item, int $key) {
            return $item['product']['id'] == $this->product_id;
        }) ?: $this->new_product;
        
        $this->fill($filter);
    }

    public function remove($tkp_version, $product_id)
    {
        $this->reset();
        $this->tkp_version = $tkp_version;
        $this->product_id = $product_id;

        $configuration = Configuration::where('tkp_version', $tkp_version)->first();
        $data = $configuration->toArray();

        $filter = collect($configuration->saved_schema['other'])->filter(function (array $item, int $key) {
            return $item['product']['id'] != $this->product_id;
        });
        $data['saved_schema']['other'] = $filter->toArray();

        $configuration->update($data);
        $configuration->save();
    }
}
