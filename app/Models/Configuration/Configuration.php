<?php

namespace App\Models\Configuration;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Configuration extends Model
{
    const PATH = 'uploads';

    protected $fillable = [
        'tkp_version',
        'image',
        'saved_schema'
    ];

    protected $casts = [
        'saved_schema' => 'array',
    ];

    protected $attributes = [
        'saved_schema' => '{}',
    ];

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->image) {
                
                $path = $model->image;

                if (File::exists($path)) {
                    File::delete($path);
                }
                
            }
        });
    }


}
