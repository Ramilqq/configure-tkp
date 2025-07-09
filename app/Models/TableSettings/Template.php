<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];


    protected static function booted(): void
    {
        static::created(function (Template $template) {
            Currency::create(['template_id' => $template->id, 'key' => 'USD', 'name' => 'Доллар', 'calc' => '0',]);
        });
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'id', 'template_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(TemplateOption::class, 'template_id', 'id');
    }
}
