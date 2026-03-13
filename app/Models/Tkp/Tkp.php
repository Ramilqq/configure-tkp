<?php

namespace App\Models\Tkp;

use App\Models\Configuration\Configuration;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tkp extends Model
{
    use HasFactory;
    
    public $pay_params_defaults  = [
        //'currency' => 'CNY',   удалены с приложения
        //'currency_val' => '',  удалены с приложения

        'marketing' => '0',
        'marketing_coef' => '0',
        'nds' => '20',
        'reserve' => '0',

        'resault_total' => '0',
        'resault_nds' => '0',
        'resault_total_nds' => '0',
    ];

    public $delivery_params_defaults  = [
        'delivery_time' => '',
        'delivery_location' => '',
        'payment_scheme' => '',
        'offer_is_valid' => '',
    ];

    protected $fillable = [
        'tkp_version',
        'user_id',
        'update_user_id',
        'project_name',
        'client_name',
        'contract_owner',
        'implementation_object',
        'industry',

        'delivery_params',
        'pay_params',

        'comments',
    ];

    protected $casts = [
        'delivery_params' => 'array',
        'pay_params' => 'array',
        'created_at' => 'datetime:d-m-Y h:m:s',
        'updated_at' => 'datetime:d-m-Y h:m:s',
    ];

    protected $attributes = [
        'delivery_params' => '{}',
        'pay_params' => '{}',
    ];

    protected static function booted(): void
    {
        // запись перед созданием
        static::created(function (Tkp $tkp) {
            
            if (!$tkp->user_id && Auth::check()) {
                $tkp->user_id = Auth::id();
            }

            // получение курса при создании ТКП
            if(!$tkp->pay_params){
                $tkp->pay_params = $tkp->pay_params_defaults;
            }

            // добавляем дефолтные параметры в доставку
            if(!$tkp->delivery_params){
                $tkp->delivery_params = $tkp->delivery_params_defaults;
            }
            
            $tkp->save();
        });

        static::updating(function (Tkp $tkp) {
            if (Auth::check()) {
                $tkp->update_user_id = Auth::id();
            }
        });

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('tkp_list');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('tkp_list');
        });

    }

    public function user(): Model
    {
        return $this->hasOne(User::class, 'id', 'user_id')->first();
    }

    public function configuration(): Model
    {
        return $this->hasOne(Configuration::class, 'tkp_version', 'tkp_version')->first();
    }

}
