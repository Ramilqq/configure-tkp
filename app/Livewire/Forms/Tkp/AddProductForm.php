<?php

namespace App\Livewire\Forms\Tkp;

use App\Models\Configuration\Configuration;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AddProductForm extends Form
{
    public int $tkp_version = 0;
    public string $product_id = '';
    public array $new_product = [
        'id' => '',
        'product_name' => '',
        'product' => [
            'id' => 0,
            'template_id' => 0,
            'hash' => '',
            'name' => '',
            'count' => 1,
            'description' => '',
            'manufacturer' => '',
            'currency_val' => 1,
            'currency' => 'RUB',
            'price' => 0,
            'delivery' => 0,
            'engineering' => [],
            'product_option' => [],
            'price_rules_applied' => [],
        ],
    ];

    protected function rules()
    {
        return [
            'new_product.id' => 'nullable',
            'new_product.product_name' => 'nullable',
            'new_product.product.id' => 'nullable',
            'new_product.product.hash' => 'nullable',
            'new_product.product.name' => 'required|min:1|max:100',
            'new_product.product.count' => 'required|min:1|max:100|numeric',
            'new_product.product.description' => 'required|min:1|max:2250',
            'new_product.product.manufacturer' => 'nullable',
            'new_product.product.currency_val' => 'nullable',
            'new_product.product.currency' => 'nullable',
            'new_product.product.price' => 'required|numeric|min:0',
            'new_product.product.delivery' => 'nullable|numeric|min:0',
            'new_product.product.engineering.*' => 'nullable',
            'new_product.product.product_option' => 'nullable',

            'new_product.product.price_rules_applied.*.value' => 'nullable|numeric|min:0',
            'new_product.product.price_rules_applied.*.rule_name' => 'nullable|string',
            'new_product.product.price_rules_applied.*.currency' => 'nullable|string',
            'new_product.product.price_rules_applied.*.rule_id' => 'nullable|numeric',
            'new_product.product.price_rules_applied.*.target' => 'nullable|string',
            'new_product.product.price_rules_applied.*.mode' => 'nullable|string',
            'new_product.product.price_rules_applied.*.currency_val' => 'nullable|numeric|min:0',
            'new_product.product.price_rules_applied.*.before' => 'nullable|numeric|min:0',
            'new_product.product.price_rules_applied.*.after' => 'nullable|numeric|min:0',

        ];
    }

    public function saveForm()
    {
        $valideate = $this->validate();
        $this->new_product = $valideate['new_product'];
        //dd($this->product_id);
        $configuration = Configuration::where('tkp_version', $this->tkp_version)->first();

        $data = $configuration->toArray();
        //dd($data);
        if(!$configuration) return [];
        
        // обновление существуюзего продукта в ткп
        if ($this->product_id) {
            
            // для продуктов из схемы конфигруатора
            $nodes = $data['saved_schema']['nodes'];
            foreach($nodes as &$item) {
                if ($this->product_id == $item['id']) {
                    $item['id'] = $this->new_product['id'];
                    $item['product_name'] = $this->new_product['product_name'];
                    $item['product']['id'] = $this->new_product['product']['id'];
                    $item['product']['name'] = $this->new_product['product']['name'];
                    $item['product']['count'] = $this->new_product['product']['count'];
                    $item['product']['description'] = $this->new_product['product']['description'];
                    $item['product']['manufacturer'] = $this->new_product['product']['manufacturer'];
                    $item['product']['currency_val'] = $this->new_product['product']['currency_val'];
                    $item['product']['currency'] = $this->new_product['product']['currency'];
                    $item['product']['price'] = $this->new_product['product']['price'];
                    $item['product']['delivery'] = $this->new_product['product']['delivery'];
                    $item['product']['engineering'] = $this->new_product['product']['engineering'];
                    $item['product']['price_rules_applied'] = $this->new_product['product']['price_rules_applied'];
                    break;
                }
            }
            $data['saved_schema']['nodes'] = $nodes;

            // для продуктов дополнительных, добавленных в ткп
            $other = $data['saved_schema']['other'];
            foreach($other as &$item) {
                if ($this->product_id == $item['id']) {
                    $item = $this->new_product;
                    break;
                }
            }
            $data['saved_schema']['other'] = $other;

            // для продуктов из схемы конфигруатора
            $connections = $data['saved_schema']['connections'];
            foreach($connections as &$item) {
                if ($this->product_id == $item['params']['id']) {
                    $item['params']['id'] = $this->new_product['id'];
                    $item['params']['product']['id'] = $this->new_product['product']['id'];
                    $item['params']['product']['name'] = $this->new_product['product']['name'];
                    $item['params']['product']['count'] = $this->new_product['product']['count'];
                    $item['params']['product']['description'] = $this->new_product['product']['description'];
                    $item['params']['product']['manufacturer'] = $this->new_product['product']['manufacturer'];
                    $item['params']['product']['currency_val'] = $this->new_product['product']['currency_val'];
                    $item['params']['product']['currency'] = $this->new_product['product']['currency'];
                    $item['params']['product']['price'] = $this->new_product['product']['price'];
                    $item['params']['product']['delivery'] = $this->new_product['product']['delivery'];
                    $item['params']['product']['engineering'] = $this->new_product['product']['engineering'];
                    $item['params']['product']['price_rules_applied'] = $this->new_product['product']['price_rules_applied'];
                    break;
                }
            }
            $data['saved_schema']['connections'] = $connections;
        // добавление дополнительного продукта в ткп
        } else {
            $this->product_id = now()->timestamp;

            $this->new_product['id']  = 'other' . $this->product_id;
            $this->new_product['product']['id']  = $this->product_id;
            $this->new_product['product']['hash']  = $this->makeFrHash($this->new_product['product']);
            
            $other = collect($data['saved_schema']['other']);
            $other->push($this->new_product);

            $data['saved_schema']['other'] = $other->toArray();
        }
        
        // очистка формы после сохранения
        //$this->resetExcept(['tkp_version', 'product_id']);

        // обновляем данные
        if ($configuration->update($data)) {
            return true;
        }
        
        return false;
    }

    public function openForm($tkp_version, $product_id)
    {
        $this->reset();
        $this->tkp_version = $tkp_version;
        $this->product_id = $product_id;

        $configuration = Configuration::where('tkp_version', $tkp_version)->first();

        $filter['new_product'] = collect($configuration->saved_schema['nodes'])->first(function (array $item, int $key) {
            return $item['id'] == $this->product_id;
        }) ?: collect($configuration->saved_schema['connections'])->first(function (array $item, int $key) {
            return $item['params']['id'] == $this->product_id;
        }) ?: collect($configuration->saved_schema['other'])->first(function (array $item, int $key) {
            return $item['id'] == $this->product_id;
        }) ?: $this->new_product ?: '';
        if (isset($filter['new_product']['params'])) $filter['new_product'] = $filter['new_product']['params'];
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
            return $item['id'] != $this->product_id;
        });
        $data['saved_schema']['other'] = $filter->toArray();

        $configuration->update($data);
        $configuration->save();
    }

    private function makeFrHash(array $options): string
    {
        return md5(json_encode($options, JSON_UNESCAPED_UNICODE));
    }
}
