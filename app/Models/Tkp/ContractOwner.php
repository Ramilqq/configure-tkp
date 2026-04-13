<?php

namespace App\Models\Tkp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ContractOwner extends Model
{
    protected $fillable = [
        'name',
    ];

    protected static function booted () {
        static::saved(function ($contract_owner) {
            // Очистка кэша при сохранении модели
            Cache::forget('contract_owner_list');
        });

        static::deleted(function ($contract_owner) {
            // Очистка кэша при удалении модели
            Cache::forget('contract_owner_list');
        });
    }
}
