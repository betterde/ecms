<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('trading_id')->index()->comment('交易ID');
            $table->unsignedBigInteger('commodity_id')->index()->comment('商品ID');
            $table->date('date')->comment('日期');
            $table->unsignedInteger('amount')->default(0)->comment('数量');
            $table->unsignedDecimal('buying', 9, 2)->default(0.00)->comment('进价');
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
        Schema::dropIfExists('pricings');
    }
}
