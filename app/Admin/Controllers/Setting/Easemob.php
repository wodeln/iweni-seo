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

namespace Zhiyi\Plus\Admin\Controllers\Setting;

use Illuminate\Http\Response;
use function Zhiyi\Plus\setting;
use Illuminate\Http\JsonResponse;
use Zhiyi\Plus\Admin\Controllers\Controller;
use Zhiyi\Plus\Admin\Requests\SetEasemob as SetEasemobRequest;

class Easemob extends Controller
{
    /**
     * Get configure.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConfigure(): JsonResponse
    {
        $settings = setting('user', 'vendor:easemob', [
            'open' => false,
            'appKey' => '',
            'clientId' => '',
            'clientSecret' => '',
            'registerType' => 0,
        ]);

        return new JsonResponse($settings, Response::HTTP_OK);
    }

    /**
     * set configure.
     * @param \Zhiyi\Plus\Admin\Requests\SetEasemob $request
     * @return \Illuminate\Http\Response
     */
    public function setConfigure(SetEasemobRequest $request)
    {
        setting('user')->set('vendor:easemob', [
            'open' => (bool) $request->input('open'),
            'appKey' => $request->input('appKey'),
            'clientId' => $request->input('clientId'),
            'clientSecret' => $request->input('clientSecret'),
            'registerType' => (int) $request->input('registerType'),
        ]);

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
