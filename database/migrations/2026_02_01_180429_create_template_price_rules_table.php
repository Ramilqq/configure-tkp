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

            $table->string('name');
            $table->string('description')->nullable();

            $table->boolean('enabled')->default(true);
            $table->unsignedInteger('sort')->default(100);

            $table->string('target_field')->default('price'); // price|delivery
            $table->string('mode')->default('add');           // replace|add|multiply

            $table->decimal('value', 18, 4)->nullable();
            $table->string('currency', 10)->default('RUB');  // RUB|USD|CNY

            /**
             * conditions: объект с двумя массивами условий (все AND):
             * {
             *   "option_conditions": [
             *     {"template_option_id": 1, "operator": "=", "value": "да"}
             *   ],
             *   "option_price_conditions": [
             *     {"template_option_id": 2, "operator": ">=", "value": "500"}
             *   ]
             * }
             * option_conditions      — проверяем ProductOption.value
             * option_price_conditions — проверяем ProductOptionPrice.price
             */
            $table->json('conditions')->nullable();

            $table->timestamps();

            $table->index(['template_id', 'enabled', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_price_rules');
    }
};
