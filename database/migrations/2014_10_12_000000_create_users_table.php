<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 贝壳书友会后台
         * 后台用户表
         */
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username')->unique()->comment('用户名');
            $table->string('password');
            $table->string('name')->nullable()->comment('用户昵称');
            $table->string('face')->nullable()->comment('头像');
            $table->string('sex')->nullable()->comment('性别');
            $table->string('mobile')->nullable()->comment('手机号');
            $table->integer('status')->default(1)->comment('用户状态');
            $table->string('type')->comment('用户类型1.管理员 2.吧主');
            $table->string('group_id')->comment('书吧ID');
            $table->string('group_name')->comment('书吧名称');
            $table->string('poster_id')->comment('书吧创建者ID');
            $table->rememberToken();
            $table->softDeletes();
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
