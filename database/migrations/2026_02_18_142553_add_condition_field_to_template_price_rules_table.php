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
        Schema::table('template_price_rules', function (Blueprint $table) {
            $table->string('condition_field', 20)->default('input')->after('condition_value');
            $table->string('text_field', 20)->default('input')->after('text_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_price_rules', function (Blueprint $table) {
            $table->dropColumn(['condition_field', 'text_field']);
        });
    }
};
