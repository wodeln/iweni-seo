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
use function Zhiyi\Component\ZhiyiPlus\PlusComponentPc\api;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentPc\formatList;

class MessageController extends BaseController
{
    public function index(Request $request)
    {
        $data['type'] = $request->query('type') ?: 0;
        $data['cid'] = $request->query('cid') ?: 0;
        $data['uid'] = $request->query('uid') ?: 0;

        return view('pcview::message.message', $data, $this->PlusData);
    }

    /**
     * 评论消息列表.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function comments(Request $request)
    {
        $after = $request->input('after') ?: 0;
        $limit = $request->input('limit') ?: 20;
        $data['comments'] = api('GET', '/api/v2/user/comments', ['after' => $after, 'limit' => $limit]);

        $return = '';
        if (! empty($data['comments'])) {
            foreach ($data['comments'] as &$v) {
                switch ($v['commentable_type']) {
                    case 'feeds':
                        $v['source_type'] = '评论了你的动态';
                        if ($v['commentable']) {
                            $v['source_url'] = Route('pc:feedread', ($v['commentable']['id'] ?? 0));
                            $v['source_content'] = $v['commentable']['feed_content'];
                            ! empty($v['commentable']['images']) && count($v['commentable']['images']) > 0 && $v['source_img'] = $this->PlusData['routes']['storage'].$v['commentable']['images'][0]['id'].'?w=35&h=35';
                        }
                        break;
                    case 'group-posts':
                        $v['source_type'] = '评论了你的帖子';
                        if ($v['commentable']) {
                            $v['source_url'] = Route('pc:grouppost', [
                                'group_id' => $v['commentable']['group_id'],
                                'post_id' => $v['commentable']['id'],
                            ]);
                            $v['source_content'] = $v['commentable']['title'];
                            ! empty($v['commentable']['images']) && count($v['commentable']['images']) > 0 && $v['source_img'] = $this->PlusData['routes']['storage'].$v['commentable']['images'][0]['id'].'?w=35&h=35';
                        }
                        break;
                    case 'news':
                        $v['source_type'] = '评论了你的文章';
                        if ($v['commentable']) {
                            $v['source_url'] = Route('pc:newsread', ($v['commentable']['id'] ?? 0));
                            $v['source_content'] = $v['commentable']['subject'];
                            $v['commentable']['image'] && $v['source_img'] = $this->PlusData['routes']['storage'].$v['commentable']['image']['id'].'?w=35&h=35';
                        }
                        break;
                    case 'questions':
                        $v['source_type'] = '评论了你的问题';
                        if ($v['commentable']) {
                            $v['source_url'] = Route('pc:questionread', ($v['commentable']['id'] ?? 0));
                            $v['source_content'] = $v['commentable']['subject'];
                            preg_match('/\@\!\[.*\]\((\d+)\)/i', $v['commentable']['body'], $imgs);
                            count($imgs) > 0 && $v['source_img'] = $this->PlusData['routes']['storage'].$imgs[1];
                        }
                        break;
                    case 'question-answers':
                        $v['source_type'] = '评论了你的回答';
                        if ($v['commentable']) {
                            $v['source_url'] = Route('pc:answeread', (['question' => $v['commentable']->question_id ?? 0, 'answer' => $v['commentable']['id'] ?? 0]));
                            $v['source_content'] = formatList($v['commentable']['body']);
                            preg_match('/\@\!\[.*\]\((\d+)\)/i', $v['commentable']['body'], $imgs);
                            count($imgs) > 0 && $v['source_img'] = $this->PlusData['routes']['storage'].$imgs[1];
                        }
                        break;
                }
            }

            $return = view('pcview::message.comments', $data, $this->PlusData)->render();
        }

        return response()->json([
            'status' => true,
            'data' => $return,
            'count' => count($data['comments']),
            'after' => last($data['comments'])['id'] ?? 0,
        ]);
    }

    /**
     * 点赞消息列表.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function likes(Request $request)
    {
        $after = $request->input('after') ?: 0;
        $limit = $request->input('limit') ?: 20;
        $data['likes'] = api('GET', '/api/v2/user/likes', ['after' => $after, 'limit' => $limit]);
        $return = '';
        if (! empty($data['likes'])) {
            foreach ($data['likes'] as &$v) {
                switch ($v['likeable_type']) {
                    case 'feeds':
                        $v['source_type'] = '赞了你的动态';
                        $v['source_url'] = Route('pc:feedread', ($v['likeable']['id'] ?? 0));
                        $v['source_content'] = $v['likeable']['feed_content'];
                        ! empty($v['likeable']['images']) && count($v['likeable']['images']) > 0 && $v['source_img'] = $this->PlusData['routes']['storage'].$v['likeable']['images'][0]['id'].'?w=35&h=35';
                        break;
                    case 'group-posts':
                        $v['source_type'] = '赞了你的帖子';
                        $v['source_url'] = Route('pc:grouppost', [
                            'group_id' => $v['likeable']['group_id'],
                            'post_id' => $v['likeable']['id'],
                        ]);
                        $v['source_content'] = $v['likeable']['summary'];
                        ! empty($v['likeable']['images']) && count($v['likeable']['images']) > 0 && $v['source_img'] = $this->PlusData['routes']['storage'].$v['likeable']['images'][0]['id'].'?w=35&h=35';
                        break;
                    case 'news':
                        $v['source_type'] = '赞了你的文章';
                        $v['source_url'] = Route('pc:newsread', ($v['likeable']['id'] ?? 0));
                        $v['source_content'] = $v['likeable']['subject'];
                        $v['likeable']['image'] && $v['source_img'] = $this->PlusData['routes']['storage'].$v['likeable']['image']['id'].'?w=35&h=35';
                        break;
                    case 'questions':
                        $v['source_type'] = '赞了你的问题';
                        $v['source_url'] = Route('pc:questionread', ($v['likeable']['id'] ?? 0));
                        $v['source_content'] = $v['likeable']['subject'];
                        preg_match('/\@\!\[.*\]\((\d+)\)/i', $v['likeable']['body'], $imgs);
                        count($imgs) > 0 && $v['source_img'] = $this->PlusData['routes']['storage'].$imgs[1];
                        break;
                    case 'question-answers':
                        $v['source_type'] = '赞了你的回答';
                        $v['source_url'] = Route('pc:answeread', (['question' => $v['likeable']->question_id ?? 0, 'answer' => $v['likeable']['id'] ?? 0]));
                        $v['source_content'] = formatList($v['likeable']['body']);
                        preg_match('/\@\!\[.*\]\((\d+)\)/i', $v['likeable']['body'], $imgs);
                        count($imgs) > 0 && $v['source_img'] = $this->PlusData['routes']['storage'].$imgs[1];
                        break;
                }
            }

            $return = view('pcview::message.likes', $data, $this->PlusData)->render();
        }

        return response()->json([
            'status' => true,
            'data' => $return,
            'count' => count($data['likes']),
            'after' => last($data['likes'])['id'] ?? 0,
        ]);
    }

    /**
     * 通知消息列表.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function notifications(Request $request)
    {
        $offset = $request->input('offset') ?: 0;
        $limit = $request->input('limit') ?: 20;
        $data['notifications'] = api('GET', '/api/v2/user/notifications', ['offset' => $offset, 'limit' => $limit]);

        // 设置已读
        api('PUT', '/api/v2/user/notifications/all');

        $return = '';
        if (! empty($data['notifications'])) {
            $return = view('pcview::message.notifications', $data, $this->PlusData)->render();
        }

        return response()->json([
            'status' => true,
            'data' => $return,
            'count' => count($data['notifications']),
        ]);
    }

    /**
     * 动态评论置顶.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pinnedFeedComment(Request $request)
    {
        $after = $request->input('after') ?: 0;
        $limit = $request->input('limit') ?: 20;
        $data['comments'] = api('GET', '/api/v2/user/feed-comment-pinneds', ['after' => $after, 'limit' => $limit]);

        $return = view('pcview::message.pinned_feedcomment', $data, $this->PlusData)->render();

        return response()->json([
            'status' => true,
            'data' => $return,
            'count' => count($data['comments']),
            'after' => last($data['comments'])['id'] ?? 0,
        ]);
    }

    /**
     * 文章评论置顶.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pinnedNewsComment(Request $request)
    {
        $after = $request->input('after') ?: 0;
        $limit = $request->input('limit') ?: 20;
        $data['comments'] = api('GET', '/api/v2/news/comments/pinneds', ['after' => $after, 'limit' => $limit]);

        $return = view('pcview::message.pinned_newscomment', $data, $this->PlusData)->render();

        return response()->json([
            'status' => true,
            'data' => $return,
            'count' => count($data['comments']),
        ]);
    }

    /**
     * 帖子评论置顶.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pinnedPostComment(Request $request)
    {
        $offset = $request->input('offset') ?: 0;
        $limit = $request->input('limit') ?: 20;
        $data['comments'] = api('GET', '/api/v2/plus-group/pinned/comments', ['offset' => $offset, 'limit' => $limit]);

        $return = view('pcview::message.pinned_postcomment', $data, $this->PlusData)->render();

        return response()->json([
            'status' => true,
            'data' => $return,
            'count' => count($data['comments']),
        ]);
    }

    /**
     * 圈子帖子置顶.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function pinnedPost(Request $request)
    {
        $offset = $request->input('offset') ?: 0;
        $limit = $request->input('limit') ?: 20;
        $data['comments'] = api('GET', '/api/v2/plus-group/pinned/posts', ['offset' => $offset, 'limit' => $limit]);

        $return = view('pcview::message.pinned_post', $data, $this->PlusData)->render();

        return response()->json([
            'status' => true,
            'data' => $return,
            'count' => count($data['comments']),
        ]);
    }

    /**
     * 联系人.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function followMutual(Request $request)
    {
        $offset = $request->input('offset') ?: 0;
        $limit = $request->input('limit') ?: 20;
        $data['users'] = api('GET', '/api/v2/user/follow-mutual', ['offset' => $offset, 'limit' => $limit]);

        $return = view('pcview::templates.follow_mutual', $data, $this->PlusData)->render();

        return response()->json([
            'status' => true,
            'data' => $return,
            'count' => count($data['users']),
        ]);
    }

    /**
     * at 我的.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function mention(Request $request)
    {
        // 拉取 mention 列表
        $after = $request->input('after');
        $limit = $request->input('limit') ?: 20;
        $data['mention'] = api('GET', '/api/v2/user/message/atme', [
            'index' => $after,
            'limit' => $limit,
        ]);

        // 获取 mention 详情
        foreach ($data['mention'] as &$mention) {
            $id = $mention['resourceable']['id'];
            $type = $mention['resourceable']['type'];
            switch ($type) {
                case 'feeds':
                    $mention['feeds'] = api('GET', '/api/v2/feeds', ['id' => $id.''])['feeds'];
                    if ($mention['feeds'][0] ?? false) {
                        $mention['feeds'] = $mention['feeds'][0];
                        $user_id = $mention['feeds']['user_id'];
                    }
                    break;
                case 'comments':
                    $mention['comments'] = api('GET', '/api/v2/comments', ['id' => $id]);
                    if ($mention['comments'][0] ?? false) {
                        $mention['comments'] = $mention['comments'][0];
                        $user_id = $mention['comments']['user_id'];
                    }
                    $mention['repostable_type'] = $mention['comments']['resourceable']['type'] ?? '';

                    switch ($mention['repostable_type']) {
                        case 'news':
                            $repo_id = $mention['comments']['resourceable']['id'];
                            $mention['repostable'] = api('GET', '/api/v2/news/'.$repo_id);
                            break;
                        case 'feeds':
                            $repo_id = $mention['comments']['resourceable']['id'];
                            $mention['repostable'] = api('GET', '/api/v2/feeds/'.$repo_id);
                            break;
                    }

                    break;
            }
            if ($user_id ?? false) {
                $mention['user'] = api('GET', '/api/v2/users/'.$user_id);
            }
        }

        $return = view('pcview::templates.mention', $data, $this->PlusData)->render();

        return response()->json([
            'status' => true,
            'data' => $return,
            'count' => count($data['mention']),
        ]);
    }
}
