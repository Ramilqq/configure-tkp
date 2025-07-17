<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class NodeGroup extends Model
{
    protected $fillable = [
        'name',
    ];



    public function nodes(): HasMany
    {
        return $this->hasMany(Node::class, 'node_group_id', 'id');
    }

}
