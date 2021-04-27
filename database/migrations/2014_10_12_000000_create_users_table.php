<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('role_of')->default(2)->comment('權限');
            $table->string('username', 20)->unique()->comment('使用者名稱');
            $table->string('password')->comment('密碼');
            $table->string('nickname', 20)->nullable()->comment('使用者暱稱');
            $table->tinyInteger('status')->default(0)->comment('帳號狀態，0 = 審核中, 1 = 正常, 2 = 無效');
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
        Schema::dropIfExists('users');
    }
}
