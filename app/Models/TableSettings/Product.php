<?php

namespace App\Models\TableSettings;

use App\Models\Tkp\Engineering;
use App\Models\Tkp\Manufacturer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Cache;

// App/Models/TableSettings/Product.php

class Product extends Model
{
    protected $fillable = [
        'hash',
        'template_id',
        'name',
        'description',
        'currency',
        'price',
        'delivery',
        'engineering',
        'drawing'   // чертеж, схема
    ];

    protected $casts = [
        'engineering' => 'array', // храните JSON компактно
    ];

    // связи с минимальным набором полей
    public function template(): HasOne
    {
        return $this->hasOne(Template::class, 'id', 'template_id')
            ->select(['id','name']);
    }

    public function productOption(): HasMany
    {
        // тянем только нужные поля
        return $this->hasMany(ProductOption::class, 'product_id', 'id')
            ->select(['id','product_id','template_option_id','value'])
            ->with(['getName' => function($q){
                $q->select(['id','key','name','fields']); // поля, которые реально используете
            }]);
    }

    public function productOptionPrice(): HasMany
    {
        return $this->hasMany(ProductOptionPrice::class, 'product_id', 'id')
            ->with(['templateOption' => function($q){
                $q->select(['id','key','name','fields']); // поля, которые реально используете
            }]);
    }

    protected static array $engDefaultsCache = [];

    protected static function booted(): void
    {
        // дефолты engineering — кешируем, чтобы не дергать БД на каждое создание
        static::creating(function (Product $product) {
            $product->getEngineering(); // прогреваем кеш
            $product->engineering = (array)($product->engineering ?? static::$engDefaultsCache);
        });

        static::created(function (Product $product) {
            $templateOption = TemplateOption::where('template_id', $product->template_id)
                ->select(['id']) // лишнего не тянем
                ->get();

            foreach ($templateOption as $opt) {
                ProductOption::create([
                    'product_id' => $product->id,
                    'template_option_id' => $opt->id,
                    'value' => '',
                ]);
            }
        });
    }

    public function allCurrency()
    {
        return Currency::VALUE;
    }

    public function getEngineering()
    {
        static::$engDefaultsCache = Cache::remember('engineering_defaults', now()->addHours(12), function () {
            return Engineering::query()
                    ->pluck('name')
                    ->mapWithKeys(fn ($n) => [$n => 0])
                    ->all();
        });

        return static::$engDefaultsCache;
    }
    
    public function getManufacturers()
    {
        return Manufacturer::all()->toArray();
    }
}

