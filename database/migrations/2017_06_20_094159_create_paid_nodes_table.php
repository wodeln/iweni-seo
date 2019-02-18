<?php

/*
 * +----------------------------------------------------------------------+
 * |                          ThinkSNS Plus                               |
 * +----------------------------------------------------------------------+
 * | Copyright (c) 2018 Chengdu ZhiYiChuangXiang Technology Co., Ltd.     |
 * +----------------------------------------------------------------------+
 * | This source file is subject to version 2.0 of the Apache license,    |
 * | that is bundled with this package in the file LICENSE, and is        |
 * | available through the world-wide-web at the following url:           |
 * | http://www.apache.org/licenses/LICENSE-2.0.html                      |
 * +----------------------------------------------------------------------+
 * | Author: Slim Kit Group <master@zhiyicx.com>                          |
 * | Homepage: www.thinksns.com                                           |
 * +----------------------------------------------------------------------+
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaidNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paid_nodes', function (Blueprint $table) {
            $table->increments('id')->comment('付费记录ID');
            $table->string('channel', 100)->comment('付费频道');
            $table->integer('raw')->comment('付费原始信息');
            $table->string('subject')->comment('付费主题');
            $table->string('body')->comment('付费内容详情');
            $table->bigInteger('amount')->unsigned()->comment('付费金额');
            $table->integer('user_id')->unsigned()->nullable()->defaul(null)->comment('用户ID，主要用于排除付费用户。');
            $table->text('extra')->nullable()->default(null)->comment('拓展信息');
            $table->timestamps();

            $table->unique(['channel', 'raw']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paid_nodes');
    }
}
