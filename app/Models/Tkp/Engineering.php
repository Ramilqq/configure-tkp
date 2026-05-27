<?php

namespace App\Models\Tkp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Engineering extends Model
{
    protected $fillable = [
        'name',
        'key',
        'price',
    ];

    protected static function booted () {
        static::saved(function ($engineering) {
            // Очистка кэша при сохранении модели
            Cache::forget('engineering_list');
            Cache::forget('engineering_defaults');
            Cache::forget('engineering_params');
        });

        static::deleted(function ($engineering) {
            // Очистка кэша при удалении модели
            Cache::forget('engineering_list');
            Cache::forget('engineering_defaults');
            Cache::forget('engineering_params');
        });
    }
}
