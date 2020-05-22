<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJournalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->index()->comment('用户ID');
            $table->string('user_type')->comment('用户类型:user,customer');
            $table->string('action')->comment('动作类型');
            $table->string('target')->nullable()->comment('操作对象');
            $table->string('method')->comment('请求类型');
            $table->string('path')->comment('请求URI');
            $table->json('query')->comment('查询参数');
            $table->json('params')->comment('请求参数');
            $table->ipAddress('ip')->nullable()->comment('IP地址');
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
        Schema::dropIfExists('journals');
    }
}
