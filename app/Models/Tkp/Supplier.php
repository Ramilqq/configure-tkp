<?php

namespace App\Models\Tkp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Supplier extends Model
{
    protected $fillable = [
        'name',
    ];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('supplier_list');
        });

        static::deleted(function () {
            Cache::forget('supplier_list');
        });
    }
}
