<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 微信公众号
         * 邀请用户表
         */
        Schema::create('invites', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index()->comment('邀请人 ID');
            $table->integer('weixin_id')->comment('被邀请人');
            $table->string('phone')->comment('被邀请人手机号');
            $table->tinyInteger('user_type')->comment('邀请人类型 1.吧主 2.普通成员');
            $table->integer('group_id')->nullable()->comment('书吧 ID');
            $table->integer('invite_status')->default(0)->comment('0.已被邀请但未注册 1.已被邀请且已注册');
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
        Schema::dropIfExists('invites');
    }
}
