<?php

namespace App\Livewire\Configuration;

use App\Models\Configuration\Node;
use App\Models\Configuration\NodeGroup;
use App\Models\TableSettings\ProductOption;
use App\Models\TableSettings\Template;
use App\Models\TableSettings\TemplateOption;
use Illuminate\Http\Request;
use Livewire\Component;

class Configuration extends Component
{
    protected $listeners = [
        'updateFilter' => 'updateFilter',
        'searchProduct' => 'searchProduct',
        'deleteProduct' => 'deleteProduct',
        'loadProduct' => 'loadProduct',
    ];

    public int $width = 0;
    public int $height = 0;

    public array $products = [];                // список продуктов выбранные на схеме
    public array $saved_schema = [];            // сохранение схемы
    public array $product_filter_select = [];   // значения в поле селект для каждой опции
    
    public array $getData = [];

   public function searchProduct($node_id = null, $conn_id = null, $type = 'nodes')
    {
        //dd($this->saved_schema, $node_id, $conn_id);
        foreach($this->saved_schema['nodes'] as $key => $nodes){
            if($nodes['id'] === $node_id){
                $this->saved_schema['nodes'][$key]['product_id'] = 1;
                $this->saved_schema['nodes'][$key]['product_name'] = 1;
                $this->saved_schema['nodes'][$key]['filter_fields'] = $this->getData;
            }
        }
        foreach($this->saved_schema['connections'] as $key => $conn){
            if($conn['params']['id'] === $conn_id){
                $this->saved_schema['connections'][$key]['params']['product_id'] = 1;
                $this->saved_schema['connections'][$key]['params']['filter_fields'] = $this->getData;
            }
        }
        
        
        //dd($this->saved_schema);

        $this->dispatch('saved_schema-updated');
    }

    public function searchProductForm()
    {
        dd($this->getData);
    }

    public function deleteProduct($id)
    {
        foreach($this->saved_schema['nodes'] as $key =>  $nodes){
            if($nodes['id'] === $id){
                unset($this->saved_schema['nodes'][$key]);
            }
        }
        foreach($this->saved_schema['connections'] as $key =>  $connection){
            if($connection['id'] === $id){
                unset($this->saved_schema['connection'][$key]);
            }
        }
        
    }

    public function loadProduct()
    {
        dd($this->saved_schema);
    }

    public function updateFilter($template_id, $node_id = null, $conn_id = null)
    {
        //dd($template_id, $node_id, $conn_id);
        $this->product_filter_select = [];
        $this->product_filter_select = TemplateOption::where('template_id', $template_id)->get()->toArray();
        //$this->saved_schema = $this->product_filter_select;
        foreach($this->saved_schema['nodes'] as $key => $node)
        {
            if ($node['id'] == $node_id)
            {
                $this->getData = $this->saved_schema['nodes'][$key]['filter_fields'];
            }
        }

        foreach($this->saved_schema['connections'] as $key => $conn)
        {
            //dd($conn);
            if ($conn['params']['id'] == $conn_id)
            {
                $this->getData = $this->saved_schema['connections'][$key]['params']['filter_fields'];
            }
        }

        //dd($this->product_filter_select, $node_id);
    }

    public function updatedProductFilter($value, $key)
    {
        //dd($value, $key);
    }

    public function mount()
    {
        //$this->updateFilter(1);
    }

    public function render()
    {
        // формирование узлов для конфигурации
        $node = Node::query()->with('nodeGroup')->with('nodeGroup.template')->with('nodeGroup.template.options')->get()->toArray();
        foreach($node as $node_key => $value_node)
        {
            $node[$node_key]['endpoints'] = json_decode($node[$node_key]['endpoints'], 1);
            $node[$node_key]['defaultName'] = $node[$node_key]['name'];
            $node[$node_key]['defaultExtra'] = '';
            foreach($node[$node_key]['endpoints'] as $anchor_key => $anchor_value)
            {
                $node[$node_key]['endpoints'][$anchor_key]['anchor'] = array_values($node[$node_key]['endpoints'][$anchor_key]['anchor']);
            }
        }
        $node = json_encode($node, JSON_UNESCAPED_UNICODE);

        // формирование груп для узлов
        $groups = NodeGroup::get()->toArray();

        // формирование кабелей, для соединений
        $cables = Template::query()->where('name', 'Кабель')->with('products')->get()->first()->toArray() ?: [];
        
        
        //$this->updateFilter();
        //dd($cables);

        return view('livewire.configuration.configuration', [
                                                            'node' => $node,
                                                            'groups' => $groups,
                                                            //'cables' => $cables,
                                                            //'product_filter' => $this->productFilter,
                                                            'product_filter_select' => $this->product_filter_select,
                                                            ]);
    }
}
