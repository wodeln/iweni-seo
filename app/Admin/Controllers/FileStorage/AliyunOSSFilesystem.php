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

namespace Zhiyi\Plus\Admin\Controllers\FileStorage;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function Zhiyi\Plus\setting;
use Illuminate\Http\JsonResponse;

class AliyunOSSFilesystem
{
    /**
     * Get Aliyun OSS filesystem.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(): JsonResponse
    {
        $configure = setting('file-storage', 'filesystems.aliyun-oss', []);
        $result = [
            'accessKeyId' => $configure['access-key-id'] ?? null,
            'accessKeySecret' => $configure['access-key-secret'] ?? null,
            'bucket' => $configure['bucket'] ?? null,
            'acl' => $configure['acl'] ?? 'public-read',
            'timeout' => $configure['timeout'] ?? 3600,
            'domain' => $configure['domain'] ?? null,
            'insideDomain' => $configure['inside-domain'] ?? null,
        ];

        return new JsonResponse($result, Response::HTTP_OK);
    }

    /**
     * Update Aliyun OSS filesystem.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request): Response
    {
        $request->validate($this->rules(), $this->messages());
        $setting = setting('file-storage');
        $setting->set('filesystems.aliyun-oss', [
            'access-key-id' => $request->input('accessKeyId'),
            'access-key-secret' => $request->input('accessKeySecret'),
            'bucket' => $request->input('bucket'),
            'acl' => $request->input('acl'),
            'timeout' => $request->input('timeout'),
            'domain' => $request->input('domain'),
            'inside-domain' => $request->input('insideDomain'),
        ]);

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    protected function rules(): array
    {
        return [
            'accessKeyId' => ['required', 'string'],
            'accessKeySecret' => ['required', 'string'],
            'bucket' => ['required', 'string'],
            'acl' => ['required', 'string', 'in:public-read-write,public-read,private'],
            'timeout' => ['required', 'integer'],
            'domain' => ['required', 'string', 'url'],
            'insideDomain' => ['required', 'string', 'url'],
        ];
    }

    protected function messages(): array
    {
        return [];
    }
}
