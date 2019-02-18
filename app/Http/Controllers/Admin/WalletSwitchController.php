<?php

declare(strict_types=1);

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

namespace Zhiyi\Plus\Http\Controllers\Admin;

use Illuminate\Http\Request;
use function Zhiyi\Plus\setting;
use Zhiyi\Plus\Http\Controllers\Controller;

class WalletSwitchController extends Controller
{
    public function show()
    {
        $settings = [
            'cash' => [
                'status' => setting('wallet', 'cash-status', true),
            ],
            'recharge' => [
                'status' => setting('wallet', 'recharge-status', true),
            ],
            'transform' => [
                'status' => setting('wallet', 'transform-status', true),
            ],
        ];

        return response()->json($settings, 200);
    }

    public function update(Request $request)
    {
        $switch = $request->input('switch');
        setting('wallet')->set([
            'cash-status' => (bool) ($switch['cash'] ?? false),
            'recharge-status' => (bool) ($switch['recharge'] ?? false),
            'transform-status' => (bool) ($switch['transform'] ?? false),
        ]);

        return response()->json(['message' => '更新成功'], 201);
    }
}
