<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $fillable = [
        'template_option_id',
        'product_id',
        'value',
    ];
}
