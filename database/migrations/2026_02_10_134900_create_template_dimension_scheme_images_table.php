<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('template_dimension_scheme_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('scheme_id')
                ->constrained('template_dimension_schemes')
                ->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->string('file_path');

            $table->unsignedInteger('sort')->default(0);
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['scheme_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_dimension_scheme_images');
    }
};
