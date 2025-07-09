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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')->constrained('templates')->onUpdate('cascade')->onDelete('cascade');
            
            $table->string('key')->nullable()->default(NULL);
            $table->string('name')->nullable()->default(NULL);
            $table->string('calc')->nullable()->default(NULL);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
