<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('template_price_rules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->constrained('templates')
                ->cascadeOnDelete();

            $table->string('name'); // RU
            $table->string('key');  // EN snake_case

            $table->boolean('enabled')->default(true);
            $table->unsignedInteger('sort')->default(100);

            $table->string('target_field')->default('price'); // price|delivery
            $table->string('mode')->default('replace');       // replace|add|multiply

            // условие (БЕЗ выбора опции): проверяем значение драйвера
            $table->string('condition_operator')->default('exists'); // exists|filled|equals|not_equals
            $table->string('condition_value')->nullable();

            // драйвер-опция: по ней берём ProductOption.value и делаем lookup/mapping
            $table->foreignId('driver_option_id')
                ->nullable()
                ->constrained('template_options')
                ->nullOnDelete();

            /**
             * mapping: массив правил диапазонов:
             * [
             *   {"from": 0, "to": 100, "value": 120000},
             *   {"from": 100, "to": 200, "value": 140000}
             * ]
             * value трактуется по mode:
             * - replace: поставить target_field = value
             * - add:     target_field = base + value
             * - multiply:target_field = base * value
             */

            $table->json('mapping')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->unique(['template_id', 'key']);
            $table->index(['template_id', 'enabled', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_price_rules');
    }
};
