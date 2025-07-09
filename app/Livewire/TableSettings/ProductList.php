<?php

namespace App\Livewire\TableSettings;

use App\Livewire\Forms\TableSettings\PriceProductForm;
use App\Livewire\Forms\TableSettings\ProductForm;
use App\Models\TableSettings\PriceProduct;
use App\Models\TableSettings\Product;
use Livewire\Component;

class ProductList extends Component
{

    protected $listeners = ['productUpdateList' => 'mount', 'productDellete' => 'productDellete', 'test' => 'test'];

    public $data = [];
    public $template_id = 0;
    public $test = [];

    public PriceProductForm $formPriceProduct;
    public ProductForm $formProduct;

    public function mount($template_id)
    {
        $this->template_id = $template_id;
        //dd($template_id);
        $this->data = Product::where('template_id', $template_id)->with('template')->with('priceProduct')->with('priceProduct.currency')->get()->toArray();
        $this->render();
    }

    public function updatedData($value, $key){
        
        //dd($this->data,$value, $key);
        //return;
        $value_explode = explode( '.', $key);
        
        if ($value_explode[1] == 'price_product'){
            $priceProduct = PriceProduct::find($this->data[$value_explode[0]]['price_product']['id']);
            
            $this->formPriceProduct->fill($this->data[$value_explode[0]]['price_product']);

            $valideate = $this->formPriceProduct->validate();
            
            $priceProduct->update($valideate);
            $priceProduct->save();
        }
        elseif ($value_explode[1] == 'template'){

        }
        else{
            $product = Product::find($this->data[$value_explode[0]]['id']);

            $this->formProduct->fill($this->data[$value_explode[0]]);

            $valideate = $this->formProduct->validate();
            
            $product->update($valideate);

            //$product->{$value_explode[1]} = $value;
            $product->save();
        }


        
    }

    public function test($id)
    {
        dd($id);
    }

    public function productDellete($id = null)
    {
        Product::find($id)->delete();

        $this->mount($this->template_id);
    }

    public function render()
    {
        
        //$data = Product::all();
        //$this->data = Product::query()->with('template')->with('priceProduct')->with('PriceProduct.currency')->get();
        //dd($this->data);
        //components.pages.product

        return view('livewire.table-settings.product-list', ['data' => $this->data]);
    }
}
