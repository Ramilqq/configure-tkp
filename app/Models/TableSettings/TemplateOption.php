<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;

class TemplateOption extends Model
{
    protected $fillable = [
        'template_id',
        'name',
        'key',
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

}
