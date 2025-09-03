<?php

use App\Models\Tkp\Delivery;
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
        Delivery::create(['name' => 'Самовывоз г. Набережные Челны']);
        Delivery::create(['name' => 'Доставка до объекта Заказчика']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
