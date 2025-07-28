<?php

use App\Models\Tkp\Industry;
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
        // автоматическое создание стандартных отраслей
        Industry::create(['name' => 'Нефтепереработка']);
        Industry::create(['name' => 'Нефтедобыча']);
        Industry::create(['name' => 'Металлургия']);
        Industry::create(['name' => 'Машиностроение']);
        Industry::create(['name' => 'Ген.компании РТ']);
        Industry::create(['name' => 'Ген.компании внешние']);
        Industry::create(['name' => 'ЖКХ']);
        Industry::create(['name' => 'Оборонный комплекс']);
        Industry::create(['name' => 'Прочие']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
