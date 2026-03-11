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
        'description',
        'enabled',
        'sort',
        'target_field',
        'mode',

        'generation_name_status',   // статус генерации названия
        'generation_name_text',     // текст для генерации названия

        // условие по значению из rulesForm[$rule->key]
        'condition_operator',
        'condition_value',
        'condition_field',

        // драйвер (диапазоны)
        'driver_option_id',
        'mapping',

        // текстовый триггер по опции товара
        'text_option_id',
        'text_operator',
        'text_value',
        'text_field',

        // фикс значение для правила
        'fixed_value',

        'meta',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'mapping' => 'array',
        'meta' => 'array',
        'fixed_value' => 'float',
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

    public function textOption(): BelongsTo
    {
        return $this->belongsTo(TemplateOption::class, 'text_option_id');
    }
}
