<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateOption extends Model
{
    protected $fillable = [
        'template_id',
        'group_id',
        'name',
        'key',
        'fields'
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    protected $attributes = [
        'fields' => '{}',
    ];

    protected static function booted(): void
    {
        static::created(function (TemplateOption $templateOption) {
            $products = Product::where('template_id', $templateOption->template_id)->get();
            foreach($products as $product){
                ProductOption::create(['template_option_id' => $templateOption->id, 'product_id' => $product->id, 'value' => '']);
            }
        });
    }

    public function productOptions(): HasMany
    {
        return $this->hasMany(ProductOption::class, 'template_option_id', 'id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(TemplateOption::class, 'group_id', 'id');
    }

}
