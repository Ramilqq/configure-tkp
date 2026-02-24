<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('template_price_rules', function (Blueprint $table) {
            // Текстовая опция (триггер). Необязательная.
            $table->unsignedBigInteger('text_option_id')->nullable()->after('driver_option_id');
            $table->string('text_operator', 20)->default('exists')->after('text_option_id');
            $table->string('text_value')->nullable()->after('text_operator');

            // Фикс значение (для режима без driver_option_id)
            $table->decimal('fixed_value', 18, 4)->nullable()->after('mode');

            $table->foreign('text_option_id')->references('id')->on('template_options')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('template_price_rules', function (Blueprint $table) {
            $table->dropForeign(['text_option_id']);
            $table->dropColumn(['text_option_id','text_operator','text_value','fixed_value']);
        });
    }
};
