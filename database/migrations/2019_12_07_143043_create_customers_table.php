<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 用户数据表
 *
 * Date: 2019/12/7
 * @author George
 */
class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('mobile')->nullable()->unique();
            $table->decimal('balance', 9, 2)->default(0.00);
            $table->string('password')->nullable();
            $table->boolean('vip')->default(false)->comment('会员');
            $table->string('province')->nullable()->comment('省');
            $table->string('municipality')->nullable()->comment('市');
            $table->string('prefecture')->nullable()->comment('县');
            $table->string('address')->nullable()->comment('地址');
            $table->uuid('referrer')->nullable();
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
        Schema::dropIfExists('customers');
    }
}
