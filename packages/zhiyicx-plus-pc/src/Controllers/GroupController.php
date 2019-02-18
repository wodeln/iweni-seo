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

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentPc\Controllers;

use Illuminate\Http\Request;
use Zhiyi\PlusGroup\Models\Group as GroupModel;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentPc\api;

class GroupController extends BaseController
{
    /**
     * 圈子首页.
     * @author 28youth
     * @param  Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->PlusData['current'] = 'group';
        $data['cates'] = api('GET', '/api/v2/plus-group/categories');
        $this->PlusData['TS'] && $data['my_group'] = api('GET', '/api/v2/plus-group/user-groups');

        // 圈子总数
        $data['category_id'] = $request->input('category_id', 0);
        $data['groups_count'] = GroupModel::where('audit', 1)->count();

        return view('pcview::group.index', $data, $this->PlusData);
    }

    /**
     * 圈子列表.
     * @author 28youth
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $type = $request->query('type', 'all');
        if ($type === 'excellent') {
            $params = [
                'excellent' => '1',
            ];
            $groups = api('GET', '/api/v2/plus-group/groups', $params);
        } elseif ($type == 'join') {
            $params = [
                'offset' => $request->query('offset', 0),
                'limit' => $request->query('limit', 15),
            ];
            $groups = api('GET', '/api/v2/plus-group/user-groups', $params);
        } elseif ($type == 'nearby') {
            $params = [
                'offset' => $request->query('offset', 0),
                'limit' => $request->query('limit', 15),
                'longitude' => $request->query('longitude'),
                'latitude' => $request->query('latitude'),
            ];
            $groups = api('GET', '/api/v2/plus-group/round/groups', $params);
        } else {
            $params = [
                'offset' => $request->query('offset', 0),
                'limit' => $request->query('limit', 15),
                'category_id' => $request->query('category_id'),
            ];
            $groups = api('GET', '/api/v2/plus-group/groups', $params);
        }

        $after = last($groups)['id'] ?? 0;
        $data['type'] = $type;
        $data['group'] = $groups;
        $groupData = view('pcview::templates.group', $data, $this->PlusData)->render();

        return response()->json([
            'status' => true,
            'data' => $groupData,
            'after' => $after,
        ]);
    }
}
