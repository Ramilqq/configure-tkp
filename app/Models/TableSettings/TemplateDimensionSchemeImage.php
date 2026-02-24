<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateDimensionSchemeImage extends Model
{
    protected $fillable = [
        'scheme_id',
        'title',
        'file_path',
        'sort',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    protected $attributes = [
        'meta' => '[]',
    ];

    public function scheme(): BelongsTo
    {
        return $this->belongsTo(TemplateDimensionScheme::class, 'scheme_id');
    }
}
