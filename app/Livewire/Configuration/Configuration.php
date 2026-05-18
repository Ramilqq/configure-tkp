<?php

namespace App\Livewire\Configuration;

use App\Models\Configuration\Configuration as TkpConfiguration;
use App\Models\Configuration\Node;
use App\Models\Configuration\NodeGroup;
use App\Models\TableSettings\Product;
use App\Models\TableSettings\ProductOptionPrice;
use App\Models\TableSettings\TemplateOption;
use App\Models\TableSettings\TemplatePriceRule;
use App\Models\Tkp\Tkp;
use App\Services\BankRequest;
use App\Services\FrReplace;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\TableSettings\TemplatePriceRuleService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TableSettings\ProductOption;
use App\Services\FrService\FrOptionsAppliedService;

class Configuration extends Component
{
    use WithFileUploads;

    protected $listeners = [
        'updateFilter' => 'updateFilter',
        'searchProduct' => 'searchProduct',
        'deleteProduct' => 'deleteProduct',
        'uploadImage' => 'uploadImage',
        'syncModalDataBack' => 'syncModalDataBack',
        'saveBtn' => 'saveBtn',
        'loadDataBtn' => 'loadDataBtn'
    ];

    public array $saved_schema = [
        'nodes' => [],
        'connections' => [],
        'other' => [],
        'page' => [
            'width' => 1000,
            'height' => 600,
        ],
    ];
    
    // сохранение схемы
    public array $product_filter_select = [];   // значения в поле селект для каждой опции
    public array $product_rules_select = [];   // значения в поле селект для каждого правила

    public array $getData = [];
    public array $getRules = [];
    public int $tkp_version = 0;
    public int $id = 0;
    public string $image_name;
    public string $image_path;
    public string $message_success = '';
    public string $message_error = '';

    public function searchProduct($node_id = null, $conn_id = null, $type = 'nodes')
    {
        $this->message_success = '';
        $this->message_error = '';

        $query = Product::query()->with('template')
            ->with(['template.priceRules'])
            ->with('productOption')
            ->with('productOption.getName');

        $banks = new BankRequest();

        $priceRules = app(TemplatePriceRuleService::class);
        $frReplace = app(FrReplace::class);
        
        // поиск узла по свойствам фильтра
        foreach($this->saved_schema['nodes'] as $key => $node){
            if($node['id'] === $node_id){

                $category = $this->resolveStrategy($node['template_id']);

                $query = $category->buildQuery($query, $this->getData);

                $productModel = $query->first();
                
                if (!$productModel) {
                    // если продукт не найден, выполняем диагностику по шагам и формируем сообщение об ошибке с детализацией
                    $steps = $category->diagnoseSearch($this->getData);
                    
                    $this->message_error = $this->formatFrSearchError($steps);
                    
                    $this->dispatch(
                        $category->getEventMessage(),
                        message_success: $this->message_success,
                        message_error: $this->message_error
                    )->to($category->getView());

                    return;
                }
                
                // сохранение базовых цен
                $basePrice = (float)$productModel->price;
                $baseDelivery = (float)$productModel->delivery;
                
                // поиск выбранных опций
                $optionsAppliedService = new (FrOptionsAppliedService::class);
                $option_applied = $optionsAppliedService->apply($this->getData, $productModel->productOption, $productModel->productOptionPrice);

                // изменение цены от опции товара, наименования и описания
                [$productModel->name,
                $productModel->description,
                $productModel->price,
                $option_price_applied] = $frReplace->apply($productModel, $option_applied);
                
                // применение правило цены, если есть
                $calc = $priceRules->apply($productModel, $this->getRules);
                
                // НЕ сохраняем модель, просто подменяем для вывода/схемы
                $productModel->price = $calc['price'];
                $productModel->delivery = $calc['delivery'];
                $applied_rules = $calc['applied_rules'];    // список примененных правил для вывода в схеме или дальнейшем сохранении
                
                // создание хэша по опциям, для количества одинаковых продуктов
                $productModel->hash = $this->makeFrHash($option_applied + $applied_rules + ['manufacturer' => $this->getData['manufacturer'] ?? '']);

                // сохраняем данные
                $product = $productModel->toArray();
                $product['price_base'] = $basePrice;
                $product['manufacturer'] = $this->getData['manufacturer'] ?? '';
                $product['delivery_base'] = $baseDelivery;
                $product['option_price_applied'] = $option_price_applied;
                $product['price_rules_applied'] = $applied_rules;
                $product['option_applied'] = $option_applied;
                $product['indicators_reliability'] =$this->getIndicatorsReliability();

                if ($product['currency'] == 'RUB') $product['currency_val'] = 1.0;
                else $product['currency_val'] = $banks->getValue($product['currency']);

                $this->saved_schema['nodes'][$key]['product_id'] = $product['id'];
                $this->saved_schema['nodes'][$key]['product_name'] = $product['name'];
                $this->saved_schema['nodes'][$key]['filter_fields'] = $this->getData;
                $this->saved_schema['nodes'][$key]['rules_fields'] = $this->getRules;
                $this->saved_schema['nodes'][$key]['product'] = $product;

                break;
            }
        }

        // поиск подключения (кабеля) по свойствам фильтра
        foreach($this->saved_schema['connections'] as $key => $conn){
            
            // если продукт уже найден по узлу, не ищем по подключению
            if (isset($productModel)) {break;}

            if($conn['params']['id'] === $conn_id){

                $category = $this->resolveStrategy($conn['params']['template_id']);
                
                $productModel = new Product;

                $productModel->id = 0;
                $productModel->template_id = 0;
                $productModel->name = $this->getData['name'];
                $productModel->description = 'Длинна: '.$this->getData['length'] . 'м.';
                $productModel->currency = 'RUB';
                $productModel->price = $this->getData['price'];
                $productModel->delivery = 0;
                $productModel->engineering = $productModel->getEngineering();;
                $productModel->drawing = '';

                // --- применяем правила цены ---
                $basePrice = $productModel->price;
                $baseDelivery = $productModel->delivery;

                $calc = $priceRules->apply($productModel, $this->getRules);

                // НЕ сохраняем, просто подменяем для вывода/схемы
                $productModel->price = $calc['price'];
                $productModel->delivery = $calc['delivery'];
                $applied_rules = $calc['applied_rules'];    // список примененных правил для вывода в схеме или дальнейшем сохранении
                $option_applied = $this->getData;
                
                // создание хэша по опциям, для количества одинаковых продуктов
                $productModel->hash = $this->makeFrHash(
                    $option_applied + $applied_rules 
                    + ['manufacturer' => $this->getData['manufacturer']]
                    + ['length' => $this->getData['length']]
                     ?? ''
                );


                $product = $productModel->toArray();
                
                $product['price_base'] = $basePrice;
                $product['delivery_base'] = $baseDelivery;
                $product['price_rules_applied'] = $calc['applied_rules'];
                $product['option_applied'] = $option_applied;
                $product['manufacturer'] = $this->getData['manufacturer'];

                if ($product['currency'] == 'RUB') $product['currency_val'] = 1.0;
                else $product['currency_val'] = $banks->getValue($product['currency']);
                
                $this->saved_schema['connections'][$key]['params']['product_id'] = $product['id'];
                $this->saved_schema['connections'][$key]['params']['filter_fields'] = $this->getData;
                $this->saved_schema['connections'][$key]['params']['rules_fields'] = $this->getRules;
                $this->saved_schema['connections'][$key]['params']['product'] = $product;

                break;
            }
        }
        //dd($this->saved_schema, $this->getData, $this->getRules, $node_id, $conn_id, $type);
        unset($query);
        if(!isset($product)) return;

        $this->message_success = 'Продукт найден и применен: ' . $product['name'];
        $this->message_error = '';

        if(isset($category)){
            $this->dispatch(
                $category->getEventMessage(),
                message_success: $this->message_success,
                message_error: $this->message_error
            )->to($category->getView());
        }  

        $this->dispatch('saved_schema-updated');
        //dd($this->saved_schema);
    }

    public function deleteProduct($id)
    {
        // удаление продукта из схемы
    }

    public function updateFilter($template_id = 0, $node_id = null, $conn_id = null)
    {
        $category = $this->resolveStrategy($template_id);

        $this->message_success = '';
        $this->message_error = '';
        $this->dispatch($category->getEventMessage(), message_success: '', message_error: '')->to($category->getView());

        // при смене шаблона подгружаем новые опции и правила для фильтра
        $this->product_filter_select = [];
        $this->product_filter_select = TemplateOption::where('template_id', $template_id)->get()->toArray();
        $this->product_rules_select = [];
        $this->product_rules_select = TemplatePriceRule::where('template_id', $template_id)->get()->toArray();

        // фильтр для узлов
        foreach($this->saved_schema['nodes'] as $key => $node)
        {
            if ($node['id'] == $node_id)
            {
                $this->getData = $category->getDefaultFilterFields($this->saved_schema['nodes'][$key]['filter_fields']);;
                $this->getRules = $this->saved_schema['nodes'][$key]['rules_fields'];
                
                break;
            }
        }

        // фильтр для подключений
        foreach($this->saved_schema['connections'] as $key => $conn)
        {
            if ($conn['params']['id'] == $conn_id)
            {
                $this->getData = $category->getDefaultFilterFields($this->saved_schema['connections'][$key]['params']['filter_fields']);;
                $this->getRules = $this->saved_schema['connections'][$key]['params']['rules_fields'];

                break;
            }
        }
        
        $this->dispatch(
            $category->getEventUpdateFilter(),
            template_id: $template_id,
            node_id: $node_id,
            conn_id: $conn_id,
            product_filter_select: $this->product_filter_select
        )->to($category->getView());

        $this->dispatch(
            $category->getEventSyncModalData(),
            getData: $this->getData,
            getRules: $this->getRules
        )->to($category->getView());
    }
    
    private function makeFrHash(array $options): string
    {
        return md5(json_encode($options, JSON_UNESCAPED_UNICODE));
    }

    public function syncModalDataBack($getData, $getRules)
    {
        // Получаем данные из модального компонента ЧРП
        $this->getData = $getData;
        $this->getRules = $getRules;
    }

    public function saveBtn()
    {
        $tkp = Tkp::findOrFail($this->id);
        $this->authorize('update', $tkp);
        
        $data = [
            'tkp_version' => $this->tkp_version,
            //'image' => $this->image_path,
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
            dd('Error TkpConfiguration not found');
        }
    }

    public function loadDataBtn()
    {
        $tkp = Tkp::findOrFail($this->id);
        $this->authorize('update', $tkp);
        
        if ($schema = TkpConfiguration::where('tkp_version', $this->tkp_version)->first())
        {
            $this->saved_schema = $schema->saved_schema;
        }
        $this->dispatch('finish-load-data');
    }

    public function saveForm()
    {

        $tkp = Tkp::findOrFail($this->id);
        $this->authorize('update', $tkp);
        
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
        //dd($node, $groups);
        return view('livewire.configuration.configuration', [
            'node' => $node,
            'groups' => $groups,
            'product_filter_select' => $this->product_filter_select,
        ]);
    }









    private function formatFrSearchError(array $steps = []): string
    {
        if (!$steps) return 'Нет данных для диагностики.';

        $html = 'Продукт не найден.<br><br>';
        $html .= 'Диагностика поиска:<br>';

        foreach ($steps as $step) {
            $html .= sprintf(
                '%s (%s %s) — было: %d, осталось: %d%s<br>',
                e($step['label']),
                e($step['operator']),
                e((string)$step['value']),
                (int)$step['before'],
                (int)$step['after'],
                $step['failed'] ? ' ❌' : ''
            );

            if ($step['failed'] && !empty($step['available_values'])) {
                $html .= 'Доступные значения у оставшихся товаров: '
                    . e(implode(', ', $step['available_values']))
                    . '<br>';
            }
        }

        $failedStep = collect($steps)->firstWhere('failed', true);

        if ($failedStep) {
            $html .= '<br><b>Причина:</b> поиск обнулился на опции "'
                . e($failedStep['label'])
                . '".';
        }

        return $html;
    }


    public function getIndicatorsReliability()
    {
        return 
        [
            [
                'group_name' => 'Показатели надежности',
                'indicators' =>
                    [
                        ['name' => 'Средняя наработка на отказ, не менее', 'value' => '50000 часов'],
                        ['name' => 'Среднее время ремонта, не более', 'value' => '20 минут'],
                        ['name' => 'Срок службы, не менее', 'value' => '20 лет'],
                        ['name' => 'Гарантийный срок эксплуатации', 'value' => '12 месяцев с момента ввода в эксплуатацию, но не более 18 месяцев с момента отгрузки оборудования'],
                    ]
            ]
        ];
    }




    private function resolveStrategy(int $template_id = 0)
    {
        return match($template_id) {
            1             => new \App\Services\ProductSearch\FrProductSearchStrategy($template_id),
            4             => new \App\Services\ProductSearch\UppProductSearchStrategy($template_id),
            0             => new \App\Services\ProductSearch\CableProductSearchStrategy($template_id),
            default       => new \App\Services\ProductSearch\GenericProductSearchStrategy($template_id),
        };
    }
}
