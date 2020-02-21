<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommoditiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commodities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('brand')->comment('品牌');
            $table->string('name')->comment('名称');
            $table->string('unit')->comment('计量单位');
            $table->string('specification')->nullable()->comment('规格');
            $table->string('category')->index()->comment('分类');
            $table->string('remark')->nullable()->comment('备注');
            $table->string('image')->nullable()->comment('图片');
            $table->string('barcode')->nullable()->comment('条形码');
            $table->unsignedInteger('amount')->default(0)->comment('数量');
            $table->string('description')->nullable()->comment('描述');
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
        Schema::dropIfExists('commodities');
    }
}
