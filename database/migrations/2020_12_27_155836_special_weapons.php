<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpecialWeapons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_weapons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20)->comment('專武名稱');
            $table->string('description')->comment('專武簡介');
            $table->json('ability')->comment('專武能力');
            $table->timestamp('apply_time')->comment('開專時間');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_weapons');
    }
}
