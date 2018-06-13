<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyBooksTable extends Migration
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
         * 0元购书 图书表
         */
        Schema::create('buy_books', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('cate')->default(0)->comment('图书类别');
            $table->tinyInteger('activity_status')->default(0)->comment('活动状态：0.未锁定|1.锁定|2.该书已被用户领取');
            $table->string('isbn')->comment('同一本书isbn是相同的');
            $table->string('title')->comment('标题');
            $table->string('subtitle')->nullable()->comment('副标题');
            $table->string('author')->comment('作者');
            $table->string('pubdate')->nullable()->comment('发布时间');
            $table->string('image')->comment('封面');
            $table->string('image_text')->comment('长图文简介');
            $table->string('translator')->nullable()->comment('翻译者');
            $table->string('catalog')->nullable()->comment('目录');
            $table->string('pages')->nullable()->comment('页数');
            $table->string('publisher')->comment('出版商');
            $table->text('author_intro')->nullable()->comment('作者简介');
            $table->text('introduction')->comment('简介');
            $table->integer('invite_total')->default(38)->comment('需要邀请的人数');
            $table->decimal('price', 8, 2)->comment('价格');
            $table->decimal('activity_price', 8, 2)->comment('活动价格');
            $table->decimal('real_price', 8, 2)->comment('实际价格');
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
        Schema::dropIfExists('buy_books');
    }
}
