<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('template_dimension_schemes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('template_id')
                ->constrained('templates')
                ->cascadeOnDelete();

            $table->string('name');
            $table->boolean('enabled')->default(true);
            $table->unsignedInteger('sort')->default(100);
            $table->string('match_mode')->default('all'); // all|any

            /**
             * conditions: array of option-conditions
             * [
             *   {"option_key":"series","op":"equals","value":"A"},
             *   {"option_key":"power","op":"in","value":["11","15"]}
             * ]
             */
            $table->json('conditions')->nullable();

            /**
             * rule_conditions: array of rule-conditions (from rules_fields)
             * [
             *   {"rule_key":"sync","op":"equals","value":"1"}
             * ]
             */
            $table->json('rule_conditions')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->index(['template_id', 'enabled', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_dimension_schemes');
    }
};
