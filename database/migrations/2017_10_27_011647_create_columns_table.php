<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColumnsTable extends Migration
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
         * 贴吧专栏表
         */
		Schema::create('columns', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->nullable()->comment('书吧id');
            $table->string('title')->nullable()->comment('专栏标题');
            $table->text('content')->nullable()->comment('专栏内容');
            $table->string('image')->nullable()->comment('专栏图片');
            $table->string('reading')->nullable()->comment('阅读量');
            $table->integer('status')->default(0)->comment('状态');
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
		Schema::drop('columns');
	}

}
