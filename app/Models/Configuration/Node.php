<?php

namespace App\Models\Configuration;

use App\Models\TableSettings\Template;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $fillable = [
        'node_group_id',
        'template_id',
        'type',
        'title',
        'name',
        'extra',
        'image',
        'endpoints',
    ];

    public function nodeGroup(): HasOne
    {
        return $this->hasOne(NodeGroup::class, 'id', 'node_group_id');
    }

    public function template(): HasOne
    {
        return $this->hasOne(Template::class, 'id', 'template_id');
    }

}
