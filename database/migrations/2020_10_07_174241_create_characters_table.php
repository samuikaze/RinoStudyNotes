<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->integer('gulid_of')->comment('公會');
            $table->integer('skill_of')->comment('技能');
            $table->integer('nickname_of')->comment('暱稱');
            $table->integer('cv_of')->comment('聲優');
            $table->string('tw_name', 10)->comment('角色中文名稱');
            $table->string('jp_name', 15)->comment('角色日文名稱');
            $table->string('s_image_url')->comment('角色小圖片網址');
            $table->string('f_image_url')->comment('角色完整圖片網址');
            $table->integer('t_image_url')->comment('角色縮圖網址');
            $table->string('description')->comment('角色簡介');
            $table->integer('ages')->comment('年齡');
            $table->integer('height')->comment('身高');
            $table->integer('weight')->comment('體重');
            $table->string('likes')->comment('喜好');
            $table->timestamp('birthday')->comment('生日');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('characters');
    }
}
