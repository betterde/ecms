<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logistics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index()->comment('订单ID');
            $table->string('number')->nullable()->comment('物流单号');
            $table->string('company')->nullable()->comment('物流公司');
            $table->string('type')->default('寄付')->comment('物流公司');
            $table->string('receiver')->comment('收件人');
            $table->string('mobile')->comment('手机号码');
            $table->string('address')->comment('收件地址');
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
        Schema::dropIfExists('logistics');
    }
}
