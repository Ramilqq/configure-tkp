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
        Engineering::create(['name' => 'po', 'price' => 100.0]);
        Engineering::create(['name' => 'kd', 'price' => 11.0]);
        Engineering::create(['name' => 'pir', 'price' => 11.0]);
        Engineering::create(['name' => 'pnr_po', 'price' => 21.0]);
        Engineering::create(['name' => 'pnr', 'price' => 1.0]);
        Engineering::create(['name' => 'smr_shmr', 'price' => 21.0]);
        Engineering::create(['name' => 'assembly', 'price' => 21.0]);
        Engineering::create(['name' => 'mounting', 'price' => 21.0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
