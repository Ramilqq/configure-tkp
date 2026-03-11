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
            $table->string('description', 255)->nullable()->default(null)->after('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('template_price_rules', function (Blueprint $table) {
            $table->dropColumn(['description']);
        });
    }
};
