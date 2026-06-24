<?php

use App\Models\Tkp\Supplier;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Supplier::create(['name' => 'ООО "ЗАВОД РУ-ДРАЙВ"']);
        Supplier::create(['name' => 'ООО НПП "РУ-ИНЖИНИРИНГ"']);
        Supplier::create(['name' => 'Заказчик']);
        Supplier::create(['name' => 'Внешний']);
    }

    public function down(): void
    {
        Supplier::truncate();
    }
};
