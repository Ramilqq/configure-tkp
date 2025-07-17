<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $fillable = [
        'node_group_id',
        'type',
        'name',
        'image',
        'endpoints',
    ];

    public function nodeGroup(): HasOne
    {
        return $this->hasOne(NodeGroup::class, 'id', 'node_group_id');
    }

}
