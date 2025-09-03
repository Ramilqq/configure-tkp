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
        Schema::create('tkps', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('tkp_version')->nullable()->default(NULL);           //версия ткп
            $table->index('tkp_version');

            $table->bigInteger('user_id')->nullable()->default(NULL);               // пользователь создал
            $table->bigInteger('update_user_id')->nullable()->default(NULL);        // пользователь обновил

            $table->string('project_name')->nullable()->default(NULL);              // имя проекта
            $table->string('client_name')->nullable()->default(NULL);               // закзчик
            $table->string('contract_owner')->nullable()->default(NULL);            // владелец договора
            $table->string('implementation_object')->nullable()->default(NULL);     // обьект внедрения
            $table->string('industry')->nullable()->default(NULL);                  // отрасль

            $table->json('delivery_params')->nullable()->default(NULL);                  // параметры доставки

            /*$table->string('delivery_time')->nullable()->default(NULL);             // срок доставки
            $table->string('delivery_location')->nullable()->default(NULL);         // место доставки
            $table->string('payment_scheme')->nullable()->default(NULL);         // схема оплаты
            $table->string('offer_is_valid')->nullable()->default(NULL);            // предложение действительно, дней*/
            
            /*$table->string('currency')->nullable()->default(NULL);                  // валюта
            $table->string('currency_val')->nullable()->default(NULL);              // курс валюты
            $table->string('bank_loss')->nullable()->default(NULL);                 // банковские потери
            $table->string('garant_fond')->nullable()->default(NULL);               // гарант фонд
            $table->string('bonuse')->nullable()->default(NULL);                    // бонус
            $table->string('nds')->nullable()->default(NULL);                       // ндс
            $table->string('stab_fond')->nullable()->default(NULL);                 // стаб фонд*/
            
            $table->json('pay_params')->nullable()->default(NULL);                  // параметры расчета

            $table->string('comments')->nullable()->default(NULL);                   // комменты к версии

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tkps');
    }
};
