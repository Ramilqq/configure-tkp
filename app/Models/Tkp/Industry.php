<?php

namespace App\Models\Tkp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Industry extends Model
{
    protected $fillable = [
        'name',
    ];

    protected static function booted () {
        static::saved(function ($industry) {
            // Очистка кэша при сохранении модели
            Cache::forget('industry_list');
        });

        static::deleted(function ($industry) {
            // Очистка кэша при удалении модели
            Cache::forget('industry_list');
        });
    }
}
