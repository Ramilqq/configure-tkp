<?php

use App\Models\Tkp\ContractOwner;
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
        // автоматическое создание стандартных Владельцев договора
        ContractOwner::create(['name' => 'ООО "КЭР-Инжиниринг"']);
        ContractOwner::create(['name' => 'ООО НПП "Ру-Инжиниринг"']);
        ContractOwner::create(['name' => 'ООО "Завод РУ-Драйв"']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
