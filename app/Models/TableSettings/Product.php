<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'template_id',
        'name',
        'description',
        'po',
        'kd',
        'pir',
        'pnr_po',
        'pnr',
        'smr_shmr',
    ];

    protected static function booted(): void
    {        
        static::created(function (Product $product) {
            $currency = Template::firstWhere('id', $product->template_id)->currency()->firstOrFail();
            PriceProduct::create(['product_id' => $product->id, 'currency_id' => $currency->id, 'price' => 0.0]);

            $templateOption = TemplateOption::where('template_id', $product->template_id)->get();
            foreach($templateOption as $templateOption){
                ProductOption::create(['product_id' => $product->id, 'template_option_id' => $templateOption->id, 'value' => '']);
            }
        });
    }

    public function template(): HasOne
    {
        return $this->hasOne(Template::class, 'id', 'template_id');
    }

    public function priceProduct(): HasOne
    {
        return $this->hasOne(PriceProduct::class);
    }

    public function productOption(): HasMany
    {
        return $this->hasMany(ProductOption::class, 'product_id', 'id');
    }

}
