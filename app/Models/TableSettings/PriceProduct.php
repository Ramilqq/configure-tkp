<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceProduct extends Model
{
    protected $fillable = [
        'product_id',
        'currency_id',
        'price',
    ];

    public function currency(): HasOne
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id');
    }

}
