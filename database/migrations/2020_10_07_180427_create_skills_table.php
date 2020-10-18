<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->integer('character_of')->comment('所屬角色');
            $table->string('skill_name', 15)->comment('技能名稱');
            $table->integer('skill_type_of')->comment('技能類型');
            $table->string('description')->comment('技能說明');
            $table->string('effect', 500)->nullable()->comment('技能效果');
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
        Schema::dropIfExists('skills');
    }
}
