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
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('node_group_id')->constrained('node_groups')->onUpdate('cascade')->onDelete('cascade');

            $table->string('type')->nullable()->default(NULL);
            $table->string('name')->nullable()->default(NULL);
            $table->longText('image')->nullable()->default(NULL);
            $table->text('endpoints')->nullable()->default(NULL);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('node');
    }
};
