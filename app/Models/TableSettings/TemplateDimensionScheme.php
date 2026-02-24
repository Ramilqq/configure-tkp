<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateDimensionScheme extends Model
{
    protected $fillable = [
        'template_id',
        'name',
        'enabled',
        'sort',
        'match_mode',
        'conditions',
        'rule_conditions',
        'meta',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'conditions' => 'array',
        'rule_conditions' => 'array',
        'meta' => 'array',
    ];

    protected $attributes = [
        'conditions' => '[]',
        'rule_conditions' => '[]',
        'meta' => '[]',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(TemplateDimensionSchemeImage::class, 'scheme_id')
            ->orderBy('sort')
            ->orderBy('id');
    }
}
