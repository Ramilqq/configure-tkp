<?php

namespace App\Models\Configuration;

use App\Models\TableSettings\Template;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class NodeGroup extends Model
{
    protected $fillable = [
        'template_id',
        'name',
    ];



    public function nodes(): HasMany
    {
        return $this->hasMany(Node::class, 'node_group_id', 'id');
    }

    public function template(): HasOne
    {
        return $this->hasOne(Template::class, 'id', 'template_id');
    }

}
