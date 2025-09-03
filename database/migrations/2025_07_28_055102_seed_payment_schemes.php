<?php

use App\Models\Tkp\PaymentScheme;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        PaymentScheme::create(['name' => 'Аванс 30%, 70% по факту готовности']);
        PaymentScheme::create(['name' => 'Аванс 30%, 40% по факту готовности, 30% в течение 30 дней с момента отгрузки']);
        PaymentScheme::create(['name' => 'Аванс 100% по факту готовности']);
        PaymentScheme::create(['name' => 'Постоплата 100% по факту отгрузки']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
