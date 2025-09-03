<?php

use App\Models\TableSettings\GroupOption;
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
        GroupOption::create(['name' => 'Входные параметры']);
        GroupOption::create(['name' => 'Выходные параметры']);
        GroupOption::create(['name' => 'Прочие параметры']);
        GroupOption::create(['name' => 'Управление']);
        GroupOption::create(['name' => 'Показатели надежности']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
