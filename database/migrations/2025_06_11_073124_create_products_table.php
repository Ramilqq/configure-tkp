<?php

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('template_id')->constrained('templates')->onUpdate('cascade')->onDelete('cascade');

            $table->string('name')->nullable()->default('name');
            $table->string('description')->nullable()->default('description');

            $table->smallInteger('po')->nullable()->default(0);
            $table->smallInteger('kd')->nullable()->default(0);
            $table->smallInteger('pir')->nullable()->default(0);
            $table->smallInteger('pnr_po')->nullable()->default(0);
            $table->smallInteger('pnr')->nullable()->default(0);
            $table->smallInteger('smr_shmr')->nullable()->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
