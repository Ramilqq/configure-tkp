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
            $table->boolean('generation_name_status', 20)->default(false)->after('mode');
            $table->string('generation_name_text', 20)->nullable()->default(null)->after('generation_name_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_price_rules', function (Blueprint $table) {
            $table->dropColumn(['generation_name_status', 'generation_name_text']);
        });
    }
};
