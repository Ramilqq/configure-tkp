<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;

class TemplateOption extends Model
{
    protected $fillable = [
        'template_id',
        'name',
        'key',
    ];
}
