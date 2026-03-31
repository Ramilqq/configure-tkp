<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOption extends Model
{
    protected $fillable = [
        'template_option_id',
        'product_id',
        'value',
    ];


    public function getName(): BelongsTo
    {
        return $this->belongsTo(TemplateOption::class, 'template_option_id', 'id');
    }


    public function templateOption(): BelongsTo
    {
        return $this->belongsTo(TemplateOption::class, 'template_option_id', 'id');
    }


    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'product_id', 'id');
    }


    public function prices(): HasMany
    {
        return $this->hasMany(ProductOptionPrice::class, 'template_option_id', 'template_option_id')
            ->where('product_id', $this->product_id);
    }
}
