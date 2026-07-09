<?php

namespace App\Livewire\Configuration;

use App\Enums\TemplateType;
use App\Models\Configuration\Configuration as TkpConfiguration;
use App\Models\Configuration\Node;
use App\Models\Configuration\NodeGroup;
use App\Models\TableSettings\Product;
use App\Models\TableSettings\TemplateOption;
use App\Models\Tkp\Tkp;
use App\Services\Configuration\SchemaProductAssembler;
use App\Services\ProductSearch\ProductSearchStrategyFactory;
use App\Services\ProductSearch\SearchDiagnosticsFormatter;
use App\Services\ProductSearch\SearchStrategyInterface;
use Livewire\Component;
use Livewire\WithFileUploads;

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

    public array $getData = [];
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

        $assembler = app(SchemaProductAssembler::class);

        // поиск узла по свойствам фильтра
        foreach($this->saved_schema['nodes'] as $key => $node){
            if($node['id'] === $node_id){

                if (!TemplateType::isBlock($node['template_id'])) {

                    $this->saved_schema['nodes'][$key]['product'] = [
                        'manufacturer' => $this->getData['manufacturer'] ?? '',
                        'suplier' => $this->getData['suplier'] ?? '',
                        ''
                    ];
                    $this->saved_schema['nodes'][$key]['filter_fields'] = $this->getData;
                    $this->dispatch('saved_schema-updated');
                    break;
                }

                $category = $this->resolveStrategy($node['template_id']);

                $query = $category->buildQuery(
                    Product::query()->with('template')
                        ->with(['template.priceRules'])
                        ->with('productOption')
                        ->with('productOption.getName'),
                    $this->getData
                );

                $productModel = $query->first();

                if (!$productModel) {
                    // если продукт не найден, выполняем диагностику по шагам и формируем сообщение об ошибке с детализацией
                    $steps = $category->diagnoseSearch($this->getData);

                    $this->message_error = app(SearchDiagnosticsFormatter::class)->format($steps);

                    $this->dispatch(
                        $category->getEventMessage(),
                        message_success: $this->message_success,
                        message_error: $this->message_error
                    )->to($category->getView());

                    return;
                }

                $product = $assembler->assembleForNode($productModel, $this->getData);

                $this->saved_schema['nodes'][$key]['product_id'] = $product['id'];
                $this->saved_schema['nodes'][$key]['product_name'] = $product['name'];
                $this->saved_schema['nodes'][$key]['filter_fields'] = $this->getData;
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

                $product = $assembler->assembleForConnection($this->getData);

                $this->saved_schema['connections'][$key]['params']['product_id'] = $product['id'];
                $this->saved_schema['connections'][$key]['params']['filter_fields'] = $this->getData;
                $this->saved_schema['connections'][$key]['params']['product'] = $product;

                break;
            }
        }

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

        // при смене шаблона подгружаем новые опции для фильтра
        $this->product_filter_select = [];
        $this->product_filter_select = TemplateOption::where('template_id', $template_id)->get()->toArray();

        // фильтр для узлов
        foreach($this->saved_schema['nodes'] as $key => $node)
        {
            //dd($this->saved_schema['nodes']);

            if ($node['id'] == $node_id)
            {
                $this->getData = $category->getDefaultFilterFields($this->saved_schema['nodes'][$key]['filter_fields'], $this->saved_schema['nodes'][$key]['type'] ?? '');
                break;
            }
        }

        // фильтр для подключений
        foreach($this->saved_schema['connections'] as $key => $conn)
        {
            if ($conn['params']['id'] == $conn_id)
            {
                $this->getData = $category->getDefaultFilterFields($this->saved_schema['connections'][$key]['params']['filter_fields']);
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
        
        //dd($this->getData);
        $this->dispatch(
            $category->getEventSyncModalData(),
            getData: $this->getData
        )->to($category->getView());
    }
    
    public function syncModalDataBack($getData)
    {
        $this->getData = $getData;
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
        $node = Node::query()->with('nodeGroup')->with('template')->with('template.options')->get()->toArray();
        
        foreach($node as $node_key => $value_node)
        {
            $node[$node_key]['endpoints'] = json_decode($node[$node_key]['endpoints'], 1);
            $node[$node_key]['defaultName'] = $node[$node_key]['name'];
            $node[$node_key]['defaultExtra'] = $node[$node_key]['extra'] ?? '';
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

    private function resolveStrategy(int $template_id = 0): SearchStrategyInterface
    {
        return app(ProductSearchStrategyFactory::class)->make($template_id);
    }
}
