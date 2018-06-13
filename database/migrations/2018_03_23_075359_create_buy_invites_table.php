<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyInvitesTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        /**
         * 小程序
         * 0元购书 邀请表
         */
		Schema::create('buy_invites', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->comment('关联buy_order表');
            $table->integer('user_id')->comment('接受邀请的用户');
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
		Schema::drop('buy_invites');
	}

}
