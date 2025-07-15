<?php

namespace App\Models\TableSettings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOption extends Model
{
    protected $fillable = [
        'template_option_id',
        'product_id',
        'value',
    ];


    public function getName(): BelongsTo
    {
        return $this->belongsTo(TemplateOption::class, 'template_option_id', 'id');
    }


}
