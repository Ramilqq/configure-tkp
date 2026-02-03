<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplatePriceRule extends Model
{
    protected $fillable = [
        'template_id',
        'name',
        'key',
        'enabled',
        'sort',
        'target_field',
        'mode',
        'condition_operator',
        'condition_value',
        'driver_option_id',
        'mapping',
        'meta',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'mapping' => 'array',
        'meta' => 'array',
    ];

    protected $attributes = [
        'meta' => '[]',
        'mapping' => '[]',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    public function driverOption(): BelongsTo
    {
        return $this->belongsTo(TemplateOption::class, 'driver_option_id');
    }
}
