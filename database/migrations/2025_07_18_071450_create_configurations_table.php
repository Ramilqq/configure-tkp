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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tkp_version')->constrained('tkps', 'tkp_version')->onUpdate('cascade')->onDelete('cascade');

            $table->string('image')->nullable()->default(NULL);
            $table->longText('saved_schema')->nullable()->default(NULL);



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
