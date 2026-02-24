<?php

namespace App\Livewire\Configuration;

use App\Models\Configuration\Configuration as TkpConfiguration;
use App\Models\Configuration\Node;
use App\Models\Configuration\NodeGroup;
use App\Models\TableSettings\Product;
use App\Models\TableSettings\TemplateOption;
use App\Models\TableSettings\TemplatePriceRule;
use App\Models\Tkp\Manufacturer;
use App\Models\Tkp\Tkp;
use App\Services\BankRequest;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Services\TableSettings\TemplatePriceRuleService;

class Configuration extends Component
{
    use WithFileUploads;

    protected $listeners = [
        'updateFilter' => 'updateFilter',
        'searchProduct' => 'searchProduct',
        'deleteProduct' => 'deleteProduct',
        'uploadImage' => 'uploadImage',
        'syncModalDataBack' => 'syncModalDataBack',
    ];

    public array $saved_schema = [
        'nodes' => [],
        'connections' => [],
        'other' => [],
        'page' => [
            'width' => 600,
            'height' => 600,
        ],
    ];            // сохранение схемы
    public array $product_filter_select = [];   // значения в поле селект для каждой опции
    public array $product_rules_select = [];   // значения в поле селект для каждого правила
    public array $product_manufacturer_select = [];   // значения в поле селект для каждого производителя
    
    public array $getData = [];
    public array $getRules = [];
    public int $tkp_version = 0;
    public int $id = 0;
    public string $image_name;
    public string $image_path;

    public function searchProduct($node_id = null, $conn_id = null, $type = 'nodes')
    {
        $query = Product::query()->with('template')
            ->with(['template.priceRules'])
            ->with('productOption')
            //->with('manufacturer')
            ->with('productOption.getName');

        $banks = new BankRequest();

        $priceRules = app(TemplatePriceRuleService::class);
        
        // поиск узла по свойствам фильтра
        foreach($this->saved_schema['nodes'] as $key => $node){
            if($node['id'] === $node_id){

                $query->where('template_id', $node['template_id']);
                
                // отделбный поиск для ЧРП
                if ($node['template_id'] == 1) {
                    $query->whereHas('productOption', function ($q) {                           // тип двигате
                        $q->where('value', $this->getData['motor_type'] ?? 'Синхронный');
                    })->whereHas('productOption', function ($q) {                               // номинальное напряжение
                        $q->where('value', '>=', $this->getData['output_voltage'] ?? '6000');
                    })->whereHas('productOption', function ($q) {                               // мощность
                        $q->where('value', '>=', $this->getData['full_power'] ?? '0');
                    })->whereHas('productOption', function ($q) {                               // номинальный ток
                        $q->where('value', '>=', $this->getData['nominalnyi_tok_ed_a'] ?? '0');
                    });
                    //dd($query->first()?->toArray(), $this->getData);
                // поиск для других шаблонов
                } else {
                    foreach ($this->getData as $value) {
                        $query->whereHas('productOption', function ($q) use ($value) {
                            $q->where('value', $value);
                        });
                    }
                }

                $productModel = $query->first() ?: null;
                //dd($productModel, $this->getData, $this->getRules);
                if(!$productModel) {return;}

                // --- применяем правила цены ---
                $basePrice = $productModel->price;
                $baseDelivery = $productModel->delivery;

                $calc = $priceRules->apply($productModel, $this->getRules);

                // НЕ сохраняем, просто подменяем для вывода/схемы
                $productModel->price = $calc['price'];
                $productModel->delivery = $calc['delivery'];
                $applied_rules = $calc['applied_rules'];    // список примененных правил для вывода в схеме или дальнейшем сохранении
                $product = $productModel->toArray();
                
                foreach ($applied_rules as $applied_rule) {
                    if (isset($applied_rule['generation_name'])) {
                        $product['name'] .= '-' . $applied_rule['generation_name'];
                    }
                }


                $product['price_base'] = $basePrice;
                $product['delivery_base'] = $baseDelivery;
                $product['price_rules_applied'] = $applied_rules;

                if ($product['currency'] == 'RUB') $product['currency_val'] = 1.0;
                else $product['currency_val'] = $banks->getValue($product['currency']);


                $this->saved_schema['nodes'][$key]['product_id'] = $product['id'];
                $this->saved_schema['nodes'][$key]['product_name'] = $product['name'];
                $this->saved_schema['nodes'][$key]['filter_fields'] = $this->getData;
                $this->saved_schema['nodes'][$key]['rules_fields'] = $this->getRules;
                $this->saved_schema['nodes'][$key]['product'] = $product;
            }
        }

        // поиск подключения (кабеля) по свойствам фильтра
        foreach($this->saved_schema['connections'] as $key => $conn){
            if($conn['params']['id'] === $conn_id){

                $query->where('template_id', $conn['params']['template_id']);

                foreach ($this->getData as $value) {
                    $query->whereHas('productOption', function ($q) use ($value) {
                        $q->where('value', $value);
                    });
                }

                $product = $query->first() ?: [];
                if(!$product) {return;}

                 $productModel = $query->first() ?: [];
                if(!$productModel) {return;}

                // --- применяем правила цены ---
                $basePrice = $productModel->price;
                $baseDelivery = $productModel->delivery;

                $calc = $priceRules->apply($productModel, $this->getRules);

                // НЕ сохраняем, просто подменяем для вывода/схемы
                $productModel->price = $calc['price'];
                $productModel->delivery = $calc['delivery'];

                $product = $productModel->toArray();
                
                $product['price_base'] = $basePrice;
                $product['delivery_base'] = $baseDelivery;
                $product['price_rules_applied'] = $calc['applied_rules'];

                if ($product['currency'] == 'RUB') $product['currency_val'] = 1.0;
                else $product['currency_val'] = $banks->getValue($product['currency']);
                
                $this->saved_schema['connections'][$key]['params']['product_id'] = $product['id'];
                $this->saved_schema['connections'][$key]['params']['filter_fields'] = $this->getData;
                $this->saved_schema['connections'][$key]['params']['rules_fields'] = $this->getRules;
                $this->saved_schema['connections'][$key]['params']['product'] = $product;
            }
        }
        
        unset($query);

        //dd($this->saved_schema, $this->getData, $this->getRules, $node_id, $conn_id, $type);
        $this->dispatch('saved_schema-updated');
    }

    public function deleteProduct($id)
    {
        //dd($id);
        //unset($this->products[$id]);
    }

    public function updateFilter($template_id, $node_id = null, $conn_id = null)
    {
        //dd($template_id, $node_id , $conn_id, $this->saved_schema );
        //$this->product_filter_select = [];
        //$this->product_filter_select = TemplateOption::where('template_id', $template_id)->get()->toArray();
        
        //$this->product_rules_select = [];
        //$this->product_rules_select = TemplatePriceRule::where('template_id', $template_id)->get()->toArray();

        //dd($this->saved_schema, $this->getData, $this->getRules);
        
        // фильтр для узлов
        foreach($this->saved_schema['nodes'] as $key => $node)
        {
            if ($node['id'] == $node_id)
            {
                if ($template_id == 1) {
                    $this->getData = [
                        'motor_type' => $this->saved_schema['nodes'][$key]['filter_fields']['motor_type'] ?? 'Синхронный',
                        'output_voltage' => $this->saved_schema['nodes'][$key]['filter_fields']['output_voltage'] ?? '6000',
                        'full_power' => $this->saved_schema['nodes'][$key]['filter_fields']['full_power'] ?? '0',
                        'nominalnyi_tok_ed_a' => $this->saved_schema['nodes'][$key]['filter_fields']['nominalnyi_tok_ed_a'] ?? '0',
                        'kpd' => $this->saved_schema['nodes'][$key]['filter_fields']['kpd'] ?? '95',
                        'cos_phi' => $this->saved_schema['nodes'][$key]['filter_fields']['cos_phi'] ?? '0.86',
                        'manufacturer_id' => $this->saved_schema['nodes'][$key]['filter_fields']['manufacturer_id'] ?? '1',
                    ];

                    $this->getRules = $this->saved_schema['nodes'][$key]['rules_fields'];
                } else {
                    $this->getData = $this->saved_schema['nodes'][$key]['filter_fields'];
                    $this->getRules = $this->saved_schema['nodes'][$key]['rules_fields'];
                }
                
            }
        }

        // фильтр для подключений
        foreach($this->saved_schema['connections'] as $key => $conn)
        {
            if ($conn['params']['id'] == $conn_id)
            {
                $this->getData = $this->saved_schema['connections'][$key]['params']['filter_fields'];
                $this->getRules = $this->saved_schema['connections'][$key]['params']['rules_fields'];
            }
        }
        
        // Отправляем данные в модальный компонент
        $this->dispatch('updateFilter', template_id: $template_id, node_id: $node_id, conn_id: $conn_id)->to('blocks.form-edit-modal-fr');
        $this->dispatch('syncModalData', getData: $this->getData, getRules: $this->getRules)->to('blocks.form-edit-modal-fr');
        //dd($this->product_filter_select, $this->product_rules_select);    
    }
    
    public function syncModalDataBack($getData, $getRules)
    {
        $this->getData = $getData;
        $this->getRules = $getRules;
    }

    public function saveForm()
    {
        $data = [
            'tkp_version' => $this->tkp_version,
            'image' => $this->image_path,
            'saved_schema' => $this->saved_schema
        ];

        // обновляем схему или сохраняем новый
        if ($schema = TkpConfiguration::where('tkp_version', $this->tkp_version)->first())
        {
            $schema->update($data);
            $schema->save();
        }
        else
        {
            TkpConfiguration::create($data);
        }

        $tkp = Tkp::find($this->id);
        $tkp->save();

        return redirect(route('tkp.delivery.edit', ['tkp_version' => $this->tkp_version, 'id' => $this->id]));
    }

    public function uploadImage($base64)
    {
        // Получаем временный файл из base64 строки
        $decoded = base64_decode($base64);
        $this->image_name = $this->id.'.jpeg';

        $path = public_path(TkpConfiguration::PATH . '/' . $this->image_name);
        file_put_contents($path, $decoded);
        $this->image_path = $path;

        $this->saveForm();
    }


    public function mount($id, $tkp_version)
    {
        $this->tkp_version = $tkp_version; 
        $this->id = $id;
        if ($schema = TkpConfiguration::where('tkp_version', $this->tkp_version)->first())
        {
            $this->saved_schema = $schema->saved_schema;
        }
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
        
        return view('livewire.configuration.configuration', [
                                                            'node' => $node,
                                                            'groups' => $groups,
                                                            'product_filter_select' => $this->product_filter_select,
                                                            ]);
    }
}
