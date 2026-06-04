<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplatePriceRule extends Model
{
    protected $fillable = [
        'template_id',
        'name',
        'description',
        'enabled',
        'sort',
        'target_field',  // price|delivery
        'mode',          // replace|add|multiply
        'value',         // числовое значение применяемое к цене
        'currency',      // RUB|USD|CNY
        'conditions',    // JSON: {option_conditions: [...], option_price_conditions: [...]}
    ];

    protected $casts = [
        'enabled'    => 'boolean',
        'value'      => 'float',
        'conditions' => 'array',
    ];

    protected $attributes = [
        'conditions' => '{"option_conditions":[],"option_price_conditions":[]}',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id');
    }
}
