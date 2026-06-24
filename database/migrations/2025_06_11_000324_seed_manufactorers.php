<?php

use App\Models\Tkp\Manufacturer;
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
        Manufacturer::create(['name' => 'ООО НПП "РУ-Инжиниринг"']);
        Manufacturer::create(['name' => 'ООО "Завод РУ-Драйв"']);
        Manufacturer::create(['name' => 'Заказчик']);
        Manufacturer::create(['name' => 'Внешний']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
