<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookCodesTable extends Migration
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
         * 纸质图书二维码表，将书与二维码绑定
         */
        Schema::create('book_codes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('book_id')->nullable()->index();
            $table->string('code')->unique()->index()->comment('二维码内容加密');
            $table->tinyInteger('activity_status')->comment('激活状态：0.未激活|1.已激活');
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
        Schema::dropIfExists('book_codes');
    }
}
