<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{


    const VALUE = [
        'RUB',
        'USD',
        'CNY',
    ];

    protected $fillable = [
        'template_id',
        'key',
        'name',
        'calc',
    ];
}
