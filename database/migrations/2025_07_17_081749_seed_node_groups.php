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
        NodeGroup::create(['name' => 'ЧРП']);
        NodeGroup::create(['name' => 'КСО']);
        NodeGroup::create(['name' => 'Блок-Бокс']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
