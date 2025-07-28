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

            $table->bigInteger('user_id')->nullable()->default(NULL);
            $table->bigInteger('update_user_id')->nullable()->default(NULL);


            $table->string('project_name')->nullable()->default(NULL);
            $table->string('client_name')->nullable()->default(NULL);
            $table->string('contract_owner')->nullable()->default(NULL);
            $table->string('implementation_object')->nullable()->default(NULL);
            $table->string('industry')->nullable()->default(NULL);





            $table->foreignId('template_id')->constrained('templates')->onUpdate('cascade')->onDelete('cascade');



            
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
