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

use Illuminate\Database\Eloquent\Builder;
use App\Models\TableSettings\ProductOption;

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
    public string $message_success = '';
    public string $message_error = '';

    public function searchProduct($node_id = null, $conn_id = null, $type = 'nodes')
    {
        $this->message_success = '';
        $this->message_error = '';

        $query = Product::query()->with('template')
            ->with(['template.priceRules'])
            ->with('productOption')
            ->with('manufacturer')
            ->with('productOption.getName');

        $banks = new BankRequest();

        $priceRules = app(TemplatePriceRuleService::class);
        
        // поиск узла по свойствам фильтра
        foreach($this->saved_schema['nodes'] as $key => $node){
            if($node['id'] === $node_id){

                if ($node['template_id'] == 1) {
                    $query->with('productOptionPrice');
                }
                $query->where('template_id', $node['template_id']);
                
                // отделбный поиск для ЧРП
                if ($node['template_id'] == 1) {
                    
                    $query->with('productOptionPrice');

                    $checks = $this->getFrSearchChecks();

                    foreach ($checks as $check) {
                        if ($check['value'] == '') continue;
                        $query = $this->applySearchCheck($query, $check);
                    }
                    
                // поиск для других шаблонов
                } else {
                    foreach ($this->getData as $value) {
                        $query->whereHas('productOption', function ($q) use ($value) {
                            $q->where('value', $value);
                        });
                    }
                }

                $productModel = $query->first() ?: null;

                if (!$productModel) {
                    if ($node['template_id'] == 1) {
                        $steps = $this->diagnoseFrSearch((int)$node['template_id'], $this->getFrSearchChecks());
                        $this->message_error = $this->formatFrSearchError($steps);
                    } else {
                        $this->message_error = 'Продукт не найден.';
                    }

                    $this->dispatch(
                        'editModalFr.getMessage',
                        message_success: $this->message_success,
                        message_error: $this->message_error
                    )->to('blocks.form-edit-modal-fr');

                    return;
                }


                $option_applied = [];
                foreach ($productModel->productOption as $productOption) {
                    $option_applied[$productOption->templateOption->key] = $productOption->value;
                }

                foreach ($productModel->productOptionPrice as $productOptionPrice) {
                    if ($this->getData[$productOptionPrice->templateOption->key] == $productOptionPrice->value) {
                        $option_applied[$productOptionPrice->templateOption->key] = $productOptionPrice->value;
                    }
                }

                // --- применяем правила цены ---
                $basePrice = $productModel->price;
                $baseDelivery = $productModel->delivery;

                // изменение цены от опции товара и сохранение схемы
                $frReplace = new FrReplace($productModel);
                [$productModel->name, $productModel->description, $productModel->price, $option_drawing_applied, $option_price_applied, $option_name_applied] = $frReplace->title($this->getData);
                                
                

                $calc = $priceRules->apply($productModel, $this->getRules);

                // НЕ сохраняем, просто подменяем для вывода/схемы
                $productModel->price = $calc['price'];
                $productModel->delivery = $calc['delivery'];
                $applied_rules = $calc['applied_rules'];    // список примененных правил для вывода в схеме или дальнейшем сохранении
                

                $productModel->fr_hash = $this->makeFrHash($option_applied + $applied_rules);


                $product = $productModel->toArray();
                
                
                
                //dd($product, $this->getData);
                $product['price_base'] = $basePrice;
                $product['delivery_base'] = $baseDelivery;
                $product['option_drawing_applied'] = $option_drawing_applied;
                $product['option_price_applied'] = $option_price_applied;
                $product['option_name_applied'] = $option_name_applied;
                $product['price_rules_applied'] = $applied_rules;

                if ($product['currency'] == 'RUB') $product['currency_val'] = 1.0;
                else $product['currency_val'] = $banks->getValue($product['currency']);

                

                $this->saved_schema['nodes'][$key]['product_id'] = $product['id'];
                $this->saved_schema['nodes'][$key]['product_name'] = $product['name'];
                $this->saved_schema['nodes'][$key]['filter_fields'] = $this->getData;
                $this->saved_schema['nodes'][$key]['rules_fields'] = $this->getRules;
                $this->saved_schema['nodes'][$key]['product'] = $product;

                //dd($product, $this->saved_schema);
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
        $this->message_success = 'Продукт найден и применен: ' . $product['name'];
        $this->message_error = '';
        $this->dispatch('editModalFr.getMessage', message_success: $this->message_success, message_error: $this->message_error)->to('blocks.form-edit-modal-fr');

        //dd($this->saved_schema, $this->getData, $this->getRules, $node_id, $conn_id, $type);
        $this->dispatch('saved_schema-updated');
    }

    public function deleteProduct($id)
    {
        // удаление продукта из схемы
    }

    public function updateFilter($template_id, $node_id = null, $conn_id = null)
    {

        $this->dispatch('editModalFr.getMessage', message_success: '', message_error: '')->to('blocks.form-edit-modal-fr');

        // при смене шаблона подгружаем новые опции и правила для фильтра
        if ($template_id != 1) {
            $this->product_filter_select = [];
            $this->product_filter_select = TemplateOption::where('template_id', $template_id)->get()->toArray();
            
            $this->product_rules_select = [];
            $this->product_rules_select = TemplatePriceRule::where('template_id', $template_id)->get()->toArray();
        }

        if ($template_id == 1) {
            $this->product_filter_select = [];
            $this->product_filter_select = TemplateOption::where('template_id', $template_id)->get()->toArray();
        }

        // фильтр для узлов
        foreach($this->saved_schema['nodes'] as $key => $node)
        {
            if ($node['id'] == $node_id)
            {
                if ($template_id == 1) {
                    $this->getData = [
                        // Характеристики электродвигателя
                        'motor_type' => $this->saved_schema['nodes'][$key]['filter_fields']['motor_type'] ?? 'A',
                        'v_output' => $this->saved_schema['nodes'][$key]['filter_fields']['v_output'] ?? '6000',
                        'p_output' => $this->saved_schema['nodes'][$key]['filter_fields']['p_output'] ?? '0',
                        'nominalnyi_tok_ed_a' => $this->saved_schema['nodes'][$key]['filter_fields']['nominalnyi_tok_ed_a'] ?? '0',
                        'kpd' => $this->saved_schema['nodes'][$key]['filter_fields']['kpd'] ?? '0',
                        'cos_phi' => $this->saved_schema['nodes'][$key]['filter_fields']['cos_phi'] ?? '0',
                        'manufacturer_id' => $this->saved_schema['nodes'][$key]['filter_fields']['manufacturer_id'] ?? '1',

                        // Дополнительные опции
                        'interface' => $this->saved_schema['nodes'][$key]['filter_fields']['interface'] ?? 'RS-485, Modbus RTU',
                        'plc_syn' => $this->saved_schema['nodes'][$key]['filter_fields']['plc_syn'] ?? 'Нет',
                        'vfd_series' => $this->saved_schema['nodes'][$key]['filter_fields']['vfd_series'] ?? 'Стандарт',
                        'material_trans' => $this->saved_schema['nodes'][$key]['filter_fields']['material_trans'] ?? 'Медь',
                        'power_cell_bypass' => $this->saved_schema['nodes'][$key]['filter_fields']['power_cell_bypass'] ?? 'Нет',
                        'sync_to_grid' => $this->saved_schema['nodes'][$key]['filter_fields']['sync_to_grid'] ?? 'Нет',
                        'ip' => $this->saved_schema['nodes'][$key]['filter_fields']['ip'] ?? 31,
                        'precharge_function' => $this->saved_schema['nodes'][$key]['filter_fields']['precharge_function'] ?? '',
                        'precharge_function_exec' => $this->saved_schema['nodes'][$key]['filter_fields']['precharge_function_exec'] ?? '',
                        'precharge' => $this->saved_schema['nodes'][$key]['filter_fields']['precharge'] ?? 'Нет',
                        'service_vfd' => $this->saved_schema['nodes'][$key]['filter_fields']['service_vfd'] ?? 'Одностороннее',
                        'bypass_vfd' => $this->saved_schema['nodes'][$key]['filter_fields']['bypass_vfd'] ?? 'Нет',
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
        
        // Отправляем данные в модальный компонент ЧРП
        if ($template_id == 1) {
            $this->dispatch('updateFilterFR', template_id: $template_id, node_id: $node_id, conn_id: $conn_id, product_filter_select: $this->product_filter_select)->to('blocks.form-edit-modal-fr');
            $this->dispatch('syncModalData', getData: $this->getData, getRules: $this->getRules)->to('blocks.form-edit-modal-fr');
        }
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
        //dd($node, $groups);
        return view('livewire.configuration.configuration', [
                                                            'node' => $node,
                                                            'groups' => $groups,
                                                            'product_filter_select' => $this->product_filter_select,
                                                            ]);
    }













































    private function getFrSearchChecks(): array
    {
        return [
            [
                'label' => 'Номинальное напряжение',
                'relation' => 'productOption',
                'template_option_id' => 14,
                'operator' => '>=',
                'value' => (int)($this->getData['v_output'] ?? 10000),
            ],
            [
                'label' => 'Мощность',
                'relation' => 'productOption',
                'template_option_id' => 12,
                'operator' => '>=',
                'value' => (int)($this->getData['p_output'] ?? 0),
            ],
            [
                'label' => 'Номинальный ток',
                'relation' => 'productOption',
                'template_option_id' => 16,
                'operator' => '>=',
                'value' => (float)($this->getData['nominalnyi_tok_ed_a'] ?? 0),
            ],
            [
                'label' => 'Наличие функции предзаряда',
                'relation' => 'productOption',
                'template_option_id' => 21,
                'operator' => '=',
                'value' => (string)($this->getData['precharge_function'] ?? null),
            ],
            [
                'label' => 'Исполнение функции предзаряда',
                'relation' => 'productOption',
                'template_option_id' => 22,
                'operator' => '=',
                'value' => (string)($this->getData['precharge_function_exec'] ?? null),
            ],
            [
                'label' => 'Наличие сервиса ЧРП',
                'relation' => 'productOption',
                'template_option_id' => 24,
                'operator' => '=',
                'value' => (string)($this->getData['service_vfd'] ?? ''),
            ],
            [
                'label' => 'Тип двигателя',
                'relation' => 'productOptionPrice',
                'template_option_id' => 7,
                'operator' => '=',
                'value' => (string)($this->getData['motor_type'] ?? 'A'),
            ],
            [
                'label' => 'Интерфейс',
                'relation' => 'productOptionPrice',
                'template_option_id' => 10,
                'operator' => '=',
                'value' => (string)($this->getData['interface'] ?? ''),
            ],
            [
                'label' => 'Наличие ПЛК синхронизации',
                'relation' => 'productOptionPrice',
                'template_option_id' => 8,
                'operator' => '=',
                'value' => (string)($this->getData['plc_syn'] ?? ''),
            ],
            [
                'label' => 'Серия ЧРП',
                'relation' => 'productOptionPrice',
                'template_option_id' => 6,
                'operator' => '=',
                'value' => (string)($this->getData['vfd_series'] ?? ''),
            ],
            [
                'label' => 'Материал трансформатора',
                'relation' => 'productOptionPrice',
                'template_option_id' => 1,
                'operator' => '=',
                'value' => (string)($this->getData['material_trans'] ?? ''),
            ],
            [
                'label' => 'Наличие байпаса силовых ячеек',
                'relation' => 'productOptionPrice',
                'template_option_id' => 2,
                'operator' => '=',
                'value' => (string)($this->getData['power_cell_bypass'] ?? ''),
            ],
            [
                'label' => 'Наличие функции синхронизации с сетью',
                'relation' => 'productOptionPrice',
                'template_option_id' => 3,
                'operator' => '=',
                'value' => (string)($this->getData['sync_to_grid'] ?? ''),
            ],
            [
                'label' => 'Степень защиты',
                'relation' => 'productOptionPrice',
                'template_option_id' => 4,
                'operator' => '=',
                'value' => (int)($this->getData['ip'] ?? 31),
            ],
            [
                'label' => 'Наличие предзаряда',
                'relation' => 'productOptionPrice',
                'template_option_id' => 5,
                'operator' => '=',
                'value' => (string)($this->getData['precharge'] ?? ''),
            ],
            [
                'label' => 'Исполнение байпаса ЧРП',
                'relation' => 'productOptionPrice',
                'template_option_id' => 9,
                'operator' => '=',
                'value' => (string)($this->getData['bypass_vfd'] ?? ''),
            ],
        ];
    }

    private function applySearchCheck(Builder $query, array $check): Builder
    {
        return $query->whereHas($check['relation'], function ($q) use ($check) {
            $q->where('template_option_id', $check['template_option_id'])
            ->where('value', $check['operator'], $check['value']);
        });
    }

    private function diagnoseFrSearch(int $templateId, array $checks): array
    {
        $productIds = Product::query()
            ->where('template_id', $templateId)
            ->pluck('id')
            ->map(fn ($id) => (int)$id)
            ->all();

        $steps = [];

        foreach ($checks as $check) {
            if ($check['value'] == '') continue;
            $beforeCount = count($productIds);

            if ($beforeCount === 0) {
                $steps[] = [
                    ...$check,
                    'before' => 0,
                    'after' => 0,
                    'failed' => true,
                    'available_values' => [],
                ];
                break;
            }

            $afterIds = Product::query()
                ->whereIn('id', $productIds)
                ->whereHas($check['relation'], function ($q) use ($check) {
                    $q->where('template_option_id', $check['template_option_id'])
                    ->where('value', $check['operator'], $check['value']);
                })
                ->pluck('id')
                ->map(fn ($id) => (int)$id)
                ->all();

            $availableValues = [];

            if (count($afterIds) === 0) {
                $availableValues = $this->getAvailableOptionValues(
                    $productIds,
                    $check['relation'],
                    (int)$check['template_option_id']
                );
            }

            $steps[] = [
                ...$check,
                'before' => $beforeCount,
                'after' => count($afterIds),
                'failed' => count($afterIds) === 0,
                'available_values' => $availableValues,
            ];

            $productIds = $afterIds;
        }

        return $steps;
    }

    private function getAvailableOptionValues(array $productIds, string $relation, int $templateOptionId): array
    {
        if (empty($productIds)) {
            return [];
        }

        $query = $relation === 'productOptionPrice'
            ? ProductOptionPrice::query()
            : ProductOption::query();

        return $query
            ->whereIn('product_id', $productIds)
            ->where('template_option_id', $templateOptionId)
            ->whereNotNull('value')
            ->pluck('value')
            ->map(fn ($value) => trim((string)$value))
            ->filter(fn ($value) => $value !== '')
            ->unique()
            ->take(10)
            ->values()
            ->all();
    }

    private function formatFrSearchError(array $steps): string
    {
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
}
