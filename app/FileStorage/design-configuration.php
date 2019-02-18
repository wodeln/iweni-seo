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

return [
    // default-filesystem
    'default-filesystem' => 'local',
    'filesystems' => [
        // filesystems.local
        'local' => [
            'disk' => 'local',
            'timeout' => 3360,
        ],
        // filesystems.aliyun-oss
        'aliyun-oss' => [
            'bucket' => null,
            'access-key-id' => null,
            'access-key-secret' => null,
            'domain' => null,
            'inside-domain' => null, // 内部请求域名
            'timeout' => 3360,
            'acl' => 'public-read', // public-read-write、public-read、private
        ],
    ],
    'channels' => [
        // channels.public
        'public' => [
            'filesystem' => '',
        ],
        // channels.protected
        'protected' => [
            'filesystem' => '',
        ],
        // channels.private
        'private' => [
            'filesystem' => '',
        ],
    ],
    // task-create-validate
    'task-create-validate' => [
        'image-min-width' => 0,
        'image-max-width' => 2800,
        'image-min-height' => 0,
        'image-max-height' => 2800,
        'file-min-size' => 2048, // 2KB
        'file-mix-size' => 2097152, // 2MB
        'file-mime-types' => [],
    ],
];
