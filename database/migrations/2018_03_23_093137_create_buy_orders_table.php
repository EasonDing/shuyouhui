<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyOrdersTable extends Migration
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
         * 0元购书 订单表
         */
		Schema::create('buy_orders', function(Blueprint $table) {
            $table->increments('id');
            $table->string('number')->unqiue()->comment('订单编号');
            $table->integer('book_id')->comment('活动图书');
            $table->integer('user_id')->comment('参与活动用户');
            $table->string('area')->comment('区域信息');
            $table->string('address')->comment('地址信息');
            $table->string('phone')->comment('手机号');
            $table->string('pay_status')->default(0)->comment('支付状态：0.待支付|1已支付');
            $table->timestamp('pay_time')->comment('支付时间');
            $table->decimal('price', 8, 2)->comment('价格');
            $table->decimal('real_price', 8, 2)->comment('实际价格');
            $table->tinyInteger('activity_status')->default(0)->comment('活动状态：0.活动进行中|1.参与活动失败|2.参与活动成功');
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
		Schema::drop('buy_orders');
	}

}
