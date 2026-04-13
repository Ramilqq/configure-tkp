<?php

namespace App\Models\Tkp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PaymentScheme extends Model
{
    protected $fillable = [
        'name',
    ];

    protected static function booted () {
        static::saved(function ($payment_scheme) {
            // Очистка кэша при сохранении модели
            Cache::forget('payment_scheme_list');
        });

        static::deleted(function ($payment_scheme) {
            // Очистка кэша при удалении модели
            Cache::forget('payment_scheme_list');
        });
    }
}
