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
        NodeGroup::create(['name' => 'ПЧ', 'template_id' => 1]);
        NodeGroup::create(['name' => 'КСО', 'template_id' => 2]);
        NodeGroup::create(['name' => 'Блок-Бокс', 'template_id' => 3]);
        NodeGroup::create(['name' => 'УПП', 'template_id' => 4]);
        NodeGroup::create(['name' => 'Двигатель', 'template_id' => 5]);
        NodeGroup::create(['name' => 'Распред устройства', 'template_id' => 5]);
        NodeGroup::create(['name' => 'Коммутация', 'template_id' => 5]);
        NodeGroup::create(['name' => 'Прочее', 'template_id' => 5]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
