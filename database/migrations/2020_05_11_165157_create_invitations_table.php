<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->uuid('initiator_id')->index()->comment('发起人ID');
            $table->string('initiator_type')->comment('发起人类型: user,customer');
            $table->string('mode')->comment('发送方式: mobile,email');
            $table->string('account')->unique()->comment('邮箱地址');
            $table->unsignedInteger('expires')->comment('有效期');
            $table->string('status')->default('unregistered')->comment('邀请状态: unregistered,registered');
            $table->string('signature')->comment('签名');
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
        Schema::dropIfExists('invitations');
    }
}
