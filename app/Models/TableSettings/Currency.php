<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'template_id',
        'key',
        'name',
        'calc',
    ];
}
