<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        /**
         * App
         * 贴吧 banner 表
         */
		Schema::create('banners', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->nullable()->comment('书吧id');
            $table->string('title')->nullable()->comment('banner标题');
            $table->text('content')->nullable()->comment('banner内容');
            $table->string('image')->nullable()->comment('banner图片');
            $table->string('reading')->nullable()->comment('阅读量');
            $table->integer('status')->default(0)->comment('状态：0.未上架 1.已上架' );
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
		Schema::drop('banners');
	}

}
