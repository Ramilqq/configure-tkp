<?php

namespace App\Models\Tkp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Delivery extends Model
{
    protected $fillable = [
        'name',
    ];

    protected static function booted () {
        static::saved(function ($delivery) {
            // Очистка кэша при сохранении модели
            Cache::forget('delivery_list');
        });

        static::deleted(function ($delivery) {
            // Очистка кэша при удалении модели
            Cache::forget('delivery_list');
        });
    }
}
