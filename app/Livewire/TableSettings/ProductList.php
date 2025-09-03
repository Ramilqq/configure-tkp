<?php

// App/Livewire/TableSettings/ProductList.php

namespace App\Livewire\TableSettings;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TableSettings\Product;
use App\Models\TableSettings\TemplateOption;
use App\Models\TableSettings\ProductOption;
use Illuminate\Support\Str;

class ProductList extends Component
{
    use WithPagination;

    protected $listeners = [
        'productUpdateList' => '$refresh',
        'productDellete'    => 'productDellete',
    ];

    public int $template_id = 0;
    public int $perPage = 10;
    public array $table_option_col = [];
    public array $optionChoices = [];
    
    /** ФИЛЬТРЫ */
    public array $filters = [
        'name'        => '',
        'description' => '',
        'price_from'  => null,
        'price_to'    => null,
        'currency'    => null,
        // динамические:
        // 'engineering' => [ 'Design' => '...', 'QA' => '...' ],
        // 'options'     => [ 'color' => 'Red', 'size' => 'XL' ],
    ];

    public function mount($template_id)
    {
        $this->template_id = (int) $template_id;

        // Названия колонок (key => name)
        $this->table_option_col = TemplateOption::query()
            ->where('template_id', $this->template_id)
            ->pluck('name', 'key')
            ->all();

        // Варианты значений из справочника для каждой опции
        $this->optionChoices = TemplateOption::query()
            ->where('template_id', $this->template_id)
            ->get(['key','fields'])
            ->mapWithKeys(function ($opt) {
                // $opt->fields уже массив (см. каст). На всякий случай нормализуем.
                $vals = is_array($opt->fields) ? $opt->fields : (json_decode($opt->fields ?? '[]', true) ?: []);
                $vals = array_values(array_unique(array_filter($vals, fn($v) => $v !== null && $v !== '')));
                sort($vals, SORT_NATURAL | SORT_FLAG_CASE);
                return [$opt->key => $vals];
            })
            ->toArray();

        // Инициализация контейнеров под фильтры
        $this->filters['engineering'] = $this->filters['engineering'] ?? [];
        $this->filters['options']     = $this->filters['options']     ?? [];
    }

    /** При любом изменении фильтров — на первую страницу */
    public function updatedFilters()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        //$product = new \App\Models\TableSettings\Product;
        //dd($product->getManufacturers());

        $query = Product::query()
            ->where('template_id', $this->template_id)
            ->select(['id','template_id','name','description','price','currency','engineering'])
            ->with([
                'template:id,name',
                'productOption' => function ($q) {
                    $q->select(['id','product_id','template_option_id','value'])
                      ->with(['getName:id,key,name,fields']);
                },
            ]);

        /** Текстовые поля */
        if ($name = trim((string)$this->filters['name'])) {
            $query->where('name', 'like', "%{$name}%");
        }
        if ($desc = trim((string)$this->filters['description'])) {
            $query->where('description', 'like', "%{$desc}%");
        }

        /** Цена */
        if ($from = $this->filters['price_from']) {
            $query->where('price', '>=', (float)$from);
        }
        if ($to = $this->filters['price_to']) {
            $query->where('price', '<=', (float)$to);
        }
        /** валюта */
        if ($currency = $this->filters['currency']) {
            $query->where('currency', $currency);
        }

        /**
         * Фильтры по ENGINEERING (JSON поле).
         * Для MySQL: JSON_EXTRACT(engineering, '$."Key"')
         * Если нужны точные числа — меняйте 'like' на сравнение.
         */
        foreach (($this->filters['engineering'] ?? []) as $engKey => $engVal) {
            if ($engVal === '' || $engVal === null) continue;

            // Экранируем кавычки в ключе для JSON path
            $safeKey = str_replace('"', '\"', $engKey);
            $path = '$."'.$safeKey.'"';

            // пример "содержит" (строчное сравнение)
            $query->whereRaw("JSON_EXTRACT(engineering, ?) LIKE ?", [$path, "%{$engVal}%"]);
            // при необходимости заменить на:
            // $query->whereRaw("CAST(JSON_EXTRACT(engineering, ?) AS DECIMAL(15,4)) >= ?", [$path, (float)$engVal]);
        }

        /**
         * Фильтры по ОПЦИЯМ (каждый key — отдельный whereHas).
         * Значение — точное совпадение (из select).
         */
        foreach (($this->filters['options'] ?? []) as $optKey => $optVal) {
            if ($optVal === '' || $optVal === null) continue;

            $query->whereHas('productOption', function ($q) use ($optKey, $optVal) {
                $q->where('value', $optVal)
                  ->whereHas('getName', function ($qq) use ($optKey) {
                      $qq->where('key', $optKey);
                  });
            });
        }

        $products = $query->paginate($this->perPage);

        return view('livewire.table-settings.product-list', [
            'products'        => $products,
            'table_option_col'=> $this->table_option_col,
        ]);
    }

    /** Сохранения — как были */
    public function saveProductField(int $productId, string $field, $value): void
    {
        $product = Product::findOrFail($productId);
        if (in_array($field, ['name','description','price', 'currency'], true)) {
            $product->{$field} = $field === 'price' ? (float)$value : $value;
            $product->save();
        }
    }

    public function saveEngineering(int $productId, string $engKey, $value): void
    {
        $product = Product::findOrFail($productId);
        $eng = (array)($product->engineering ?? []);
        $eng[$engKey] = is_numeric($value) ? (float)$value : $value;
        $product->engineering = $eng;
        $product->save();
    }

    public function saveProductOption(int $productOptionId, $value): void
    {
        $opt = ProductOption::findOrFail($productOptionId);
        $opt->value = (string)$value;
        $opt->save();
    }

    public function resetFilters(): void
    {
        $this->filters = [
            'name'        => '',
            'description' => '',
            'price_from'  => null,
            'price_to'    => null,
            'engineering' => [],
            'options'     => [],
        ];
        $this->resetPage();
    }

    public function productDellete($id = null)
    {
        Product::findOrFail($id)->delete();
        $this->resetPage();
    }

    public function updated($name, $value)
    {
        // Любое изменение внутри filters.* -> на первую страницу
        if (Str::startsWith($name, 'filters.')) {
            $this->resetPage();
        }
    }

}
