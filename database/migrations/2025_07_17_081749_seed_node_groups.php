<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Configuration\NodeGroup;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // автоматическое создание стандартных групп для узлов в конфигураторе
        NodeGroup::create(['name' => 'ПЧ']);
        NodeGroup::create(['name' => 'КСО']);
        NodeGroup::create(['name' => 'Блок-Бокс']);
        NodeGroup::create(['name' => 'УПП']);
        NodeGroup::create(['name' => 'Двигатель']);
        NodeGroup::create(['name' => 'Распред устройства']);
        NodeGroup::create(['name' => 'Коммутация']);
        NodeGroup::create(['name' => 'Прочее']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
