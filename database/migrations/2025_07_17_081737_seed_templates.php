<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\TableSettings\Template;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // создание стандартных шаблонов
        Template::create(['name' => 'ЧРП', 'description' => 'Шаблон для ЧРП']);
        Template::create(['name' => 'КСО', 'description' => 'Шаблон для КСО']);
        Template::create(['name' => 'Кабель', 'description' => 'Шаблон для Кабеля']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
