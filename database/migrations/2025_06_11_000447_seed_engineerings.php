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
        Engineering::create(['name' => 'ПСД (н.ч*х)', 'key' => 'psd',         'price' => 1.0]);
        Engineering::create(['name' => 'КД (н.ч*х)', 'key' => 'kd',          'price' => 1.0]);
        Engineering::create(['name' => 'ПО (н.ч*х)', 'key' => 'po',          'price' => 1.0]);
        Engineering::create(['name' => 'Сборка (н.ч*х)', 'key' => 'assembly',    'price' => 1.0]);
        Engineering::create(['name' => 'Тестирование (н.ч*х)', 'key' => 'test',        'price' => 1.0]);
        Engineering::create(['name' => 'СМР/ШМР (н.ч*х)', 'key' => 'smr_shmr',    'price' => 1.0]);
        Engineering::create(['name' => 'ПНР (н.ч*х)', 'key' => 'pnr',         'price' => 1.0]);
        Engineering::create(['name' => 'ПНР ПО (н.ч*х)', 'key' => 'pnr_po',      'price' => 1.0]);

        Engineering::create(['name' => 'ТО (руб.)', 'key' => 'to',      'price' => 1.0]);
        Engineering::create(['name' => 'Расход на упр. работами (руб.)', 'key' => 'costs_works',      'price' => 1.0]);

        Engineering::create(['name' => 'Расходы на ТКП (руб.)', 'key' => 'cost_tkp',         'price' => 0.0]);
        Engineering::create(['name' => 'Премия за управление проектом (руб.)', 'key' => 'bonuse_manager',      'price' => 0.0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
