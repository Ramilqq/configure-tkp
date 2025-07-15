<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ComponentGroup extends Model
{
    protected $table = 'cfg_component_groups'; 
    protected $fillable = [
        'name',
    ];



    public function components(): HasMany
    {
        return $this->hasMany(Component::class, 'cfg_component_group_id', 'id');
    }

}
