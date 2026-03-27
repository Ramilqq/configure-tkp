<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOptionPrice extends Model
{
    protected $fillable = [
        'template_option_id',
        'product_id',
        'value',
        'price',
        'drawing',
        'airflow',
        'dimension',
        'weight',
        'service',
        'rename_title',
        'rename_title_end',
        'rename_description',
    ];
    
    public function templateOption(): BelongsTo
    {
        return $this->belongsTo(TemplateOption::class, 'template_option_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}