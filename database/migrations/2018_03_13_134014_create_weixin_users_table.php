<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeixinUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 微信用户信息表
         */
        Schema::create('weixin_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unionid')->unique();
            $table->string('openid');
            $table->integer('user_id')->unique()->nullable();
            $table->string('nickname')->nullable();
            $table->tinyInteger('sex')->default(0)->comment('性别：0.未知|1.男|2.女');
            $table->string('language')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('country')->nullable();
            $table->string('headimgurl')->nullable();
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
        Schema::dropIfExists('weixin_users');
    }
}
