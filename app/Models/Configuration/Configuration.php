<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'image',
        'saved_schema'
    ];
}
