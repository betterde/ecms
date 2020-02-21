<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 交易数据表
 *
 * Date: 2019/12/7
 * @author George
 */
class CreateTradingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tradings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id')->comment('订单ID');
            $table->unsignedBigInteger('commodity_id')->comment('商品ID');
            $table->unsignedInteger('amount')->default(0)->comment('数量');
            $table->unsignedDecimal('price', 9, 2)->default(0.00)->comment('单价');
            $table->unsignedDecimal('total', 9, 2)->default(0.00)->comment('总价');
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
        Schema::dropIfExists('tradings');
    }
}
