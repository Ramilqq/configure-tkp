<?php

use App\Models\Tkp\Engineering;
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
        Engineering::create(['name' => 'ПО', 'key' => 'po', 'price' => 100.0]);
        Engineering::create(['name' => 'КД', 'key' => 'kd', 'price' => 11.0]);
        Engineering::create(['name' => 'ПИР', 'key' => 'pir', 'price' => 11.0]);
        Engineering::create(['name' => 'ПНР ПО', 'key' => 'pnr_po', 'price' => 21.0]);
        Engineering::create(['name' => 'ПНР', 'key' => 'pnr', 'price' => 1.0]);
        Engineering::create(['name' => 'СМР/ШМР', 'key' => 'smr_shmr', 'price' => 21.0]);
        Engineering::create(['name' => 'Сборка', 'key' => 'assembly', 'price' => 21.0]);
        Engineering::create(['name' => 'Монтаж', 'key' => 'mounting', 'price' => 21.0]);
        Engineering::create(['name' => 'ТКП', 'key' => 'tkp', 'price' => 21.0]);
        Engineering::create(['name' => 'ПСД', 'key' => 'psd', 'price' => 21.0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
