<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 客户订单数据表
 *
 * Date: 2019/12/7
 * @author George
 */
class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type')->comment('交易类型：采购、销售');
            $table->unsignedDecimal('total', 9, 2)->default(0.00)->comment('总金额');
            $table->unsignedInteger('discount')->default(100)->comment('折扣');
            $table->unsignedDecimal('actual', 9, 2)->default(0.00)->comment('实际金额');
            $table->unsignedDecimal('cost', 9, 2)->default(0.00)->comment('成本');
            $table->unsignedDecimal('profit', 9, 2)->default(0.00)->comment('利润');
            $table->date('date')->index()->comment('日期');
            $table->uuid('customer_id')->nullable()->index()->comment('购买人ID');
            $table->string('remark')->nullable()->comment('备注');
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
        Schema::dropIfExists('orders');
    }
}
