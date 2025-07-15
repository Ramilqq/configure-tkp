<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Component extends Model
{
    protected $table = 'cfg_components'; 

    protected $fillable = [
        'cfg_component_group_id',
        'type',
        'name',
        'image',
        'endpoints',
    ];

    public function componentGroup(): HasOne
    {
        return $this->hasOne(ComponentGroup::class, 'id', 'cfg_component_group_id');
    }

}
