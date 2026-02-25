<?php

namespace App\Models\Tkp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Manufacturer extends Model
{
    protected $fillable = [
        'name',
    ];

    protected static function booted () {
        static::saved(function ($engineering) {
            // Очистка кэша при сохранении модели
            Cache::forget('manufacturer_list');
        });

        static::deleted(function ($engineering) {
            // Очистка кэша при удалении модели
            Cache::forget('manufacturer_list');
        });
    }
}
