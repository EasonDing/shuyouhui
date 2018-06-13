<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('news', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->comment('接收者ID');
            $table->string('username')->comment('接收者名称');
            $table->integer('user_type')->comment('接收者类型:1、全员消息 2、吧主消息 3、私人');
            $table->string('title')->comment('个推标题');
            $table->string('content')->comment('个推内容');
            $table->integer('status')->comment('消息状态: 1、成功、2失败');
            $table->string('url')->nullable()->comment('url链接');
            $table->string('send_id')->comment('发送者ID');
            $table->string('news_type')->comment('类型:1、公共, 2、私人');
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
		Schema::drop('news');
	}

}
