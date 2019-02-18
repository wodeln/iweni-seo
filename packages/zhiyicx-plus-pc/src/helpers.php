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

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentPc;

use Auth;
use Session;
use HTMLPurifier;
use Carbon\Carbon;
use GuzzleHttp\Client;
use HTMLPurifier_Config;
use Zhiyi\Plus\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/**
 * [formatContent 动态列表内容处理].
 * @author Foreach
 * @param  [string] $content [内容]
 * @return [string]
 */
function formatContent($content)
{
    // 链接替换
    $content = preg_replace_callback('/((?:https?|mailto|ftp):\/\/([^\x{2e80}-\x{9fff}\s<\'\"“”‘’，。}]*)?)/u', function ($url) {
        return '<a class="mcolor" href="'.$url[0].'">访问链接+</a>';
    }, $content);

    // 回车替换
    $pattern = ["\r\n", "\n", "\r"];
    $replace = '<br>';
    $content = str_replace($pattern, $replace, $content);

    // 过滤xss
    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.Allowed', 'br,a[href|class]');
    $purifier = new HTMLPurifier($config);
    $content = $purifier->purify($content);

    // at 用户替换为链接
    $content = preg_replace_callback('/\x{00ad}@((?:[^\/]+?))\x{00ad}/iu', function ($match) {
        $username = $match[1];
        $url = route('pc:mine', [
            'user' => $username,
        ]);

        return sprintf('<a href="%s">@%s</a>', $url, $username);
    }, $content);

    return $content;
}

/**
 * [inapi 内部请求（弃用）].
 * @author Foreach
 * @param  string  $method   [请求方式]
 * @param  string  $url      [地址]
 * @param  array   $params   [参数]
 * @param  int $instance
 * @param  int $original
 * @return
 */
function inapi($method = 'POST', $url = '', $params = [], $instance = 1, $original = 1)
{
    $request = Request::create($url, $method, $params);
    $request->headers->add(['Accept' => 'application/json', 'Authorization' => 'Bearer '.Session::get('token')]);

    // 注入JWT请求单例
    app()->resolving(\Tymon\JWTAuth\JWT::class, function ($jwt) use ($request) {
        $jwt->setRequest($request);

        return $jwt;
    });
    Auth::guard('api')->setRequest($request);

    // 解决获取认证用户
    $request->setUserResolver(function () {
        return Auth::user('api');
    });

    // 解决请求传参问题
    if ($instance) { // 获取登录用户不需要传参
        app()->instance(Request::class, $request);
    }

    $response = Route::dispatch($request);

    return $original ? $response->original : $response;
}

/**
 * [api].
 * @author Foreach
 * @param  string  $method   [请求方式]
 * @param  string  $url      [地址]
 * @param  array   $params   [参数]
 * @return
 */
function api($method = 'POST', $url = '', $params = [])
{
    $client = new Client([
        'base_uri' => config('app.url'),
    ]);

    $headers = ['Accept' => 'application/json', 'Authorization' => 'Bearer '.Session::get('token')];
    if ($method == 'GET') {
        $response = $client->request($method, $url, [
            'query' => $params,
            'headers' => $headers,
            'http_errors' => false,
        ]);
    } else {
        $response = $client->request($method, $url, [
            'form_params' => $params,
            'headers' => $headers,
            'http_errors' => false,
        ]);
    }

    return json_decode($response->getBody(), true);
}

/**
 * [getTime 时间转换].
 * @author Foreach
 * @param  [string] $time [时间]
 * @return
 */
function getTime($time)
{
    // 本地化
    $time = Carbon::parse($time);
    Carbon::setLocale('zh');
    $timezone = isset($_COOKIE['customer_timezone']) ? $_COOKIE['customer_timezone'] : 0;
    if (Carbon::now()->subHours(24) < $time && $time < Carbon::now()) {
        // 一天内显示友好时间
        return $time->diffForHumans();
    } elseif ((Carbon::now()->subHours(24) > $time) && (Carbon::now()->subHours(48) < $time)) {
        // 一到两天内显示昨天+时分
        return '昨天 '.$time->addHours($timezone)->format('H:i');
    } elseif (Carbon::now()->subHours(48) > $time && $time->addHours($timezone)->isCurrentYear()) {
        // 两天以上，今年内显示月日时分
        return $time->format('m-d H:i');
    } else {
        // 其他时间返回年月日时分
        return $time->format('Y-m-d H:i');
    }
}

/**
 * [getImageUrl 获取图片地址].
 * @author Foreach
 * @param  array   $image  [图片数组]
 * @param  [type]  $width  [宽度]
 * @param  [type]  $height [高度]
 * @param  bool $cut    [是否裁剪]
 * @param  int $blur   [是否高斯模糊]
 * @return [string]
 */
function getImageUrl($image = [], $width, $height, $cut = true, $blur = 0)
{
    if (! $image) {
        return false;
    }
    // 高斯模糊参数
    $b = $blur != 0 ? '&b='.$blur : '';

    // 裁剪
    $file = $image['file'] ?? $image['id'];
    if ($cut) {
        $size = explode('x', $image['size']);
        if ($size[0] > $size[1]) {
            $width = number_format($height / $size[1] * $size[0], 2, '.', '');
        } else {
            $height = number_format($width / $size[0] * $size[1], 2, '.', '');
        }

        return asset('/api/v2/files/'.$file).'?&w='.$width.'&h='.$height.$b.'&token='.Session::get('token');
    } else {
        return asset('/api/v2/files/'.$file).'?token='.Session::get('token').$b;
    }
}

/**
 * [cacheClear 清理缓存].
 * @author Zsyd
 * @return
 */
function cacheClear()
{
    return Artisan::call('cache:clear');
}

/**
 * [getAvatar 获取头像].
 * @author Foreach
 * @param  [type]  $user  [用户数组]
 * @param  int $width [宽度]
 * @return [string]
 */
function getAvatar($user, $width = 0)
{
    if (empty($user['avatar']) || ! $user['avatar']) {
        switch ($user['sex']) {
            case 1:
                return asset('assets/pc/images/pic_default_man.png');
            case 2:
                return asset('assets/pc/images/pic_default_woman.png');
        }

        return asset('assets/pc/images/pic_default_secret.png');
    }

    $avatar = $user['avatar'];
    if ($avatar instanceof \Zhiyi\Plus\FileStorage\FileMetaInterface) {
        $avatar = $avatar->toArray();
    }
    if ($avatar['vendor'] === 'local' && $width) {
        return sprintf('%s?rule=w_%s', $avatar['url'], $width);
    } elseif ($avatar['vendor'] === 'aliyun-oss' && $width) {
        return sprintf('%s?rule=image/resize,w_%s', $avatar['url'], $width);
    }

    return $avatar['url'];
}

/**
 * [formatMarkdown 转换markdown].
 * @author Foreach
 * @param  [string] $body [内容]
 * @return [string] [html]
 */
function formatMarkdown($body)
{
    // 图片替换
    $body = preg_replace('/\@\!\[(.*?)\]\((\d+)\)/i', '![$1]('.getenv('APP_URL').'/api/v2/files/$2)', $body);

    // $content = htmlspecialchars_decode(\Parsedown::instance()->setMarkupEscaped(true)->text($body));
    // if (!strip_tags($content)) {
    //     $content = preg_replace_callback('/\[\]\((.*?)\)/i', function($url){
    //         return '<p><a href="'.$url[1].'">'.$url[1].'</a></p>';
    //     }, $body);
    // }

    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.Allowed', 'br,a[href]');
    $purifier = new HTMLPurifier($config);
    $body = $purifier->purify($body);
    $content = \Parsedown::instance()->text($body);

    return $content;
}

/**
 * @author Foreach
 * @param  [string] $body [内容]
 * @return [string] [html]
 */
function formatList($body)
{
    $body = preg_replace('/\@\!\[(.*?)\]\((\d+)\)/', '[图片]', $body);

    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.Allowed', 'br,a[href]');
    $purifier = new HTMLPurifier($config);
    $body = $purifier->purify($body);
    $content = \Parsedown::instance()->text($body);

    return  $content;
}

/**
 * [getUserInfo 获取用户信息].
 * @author Foreach
 * @param  [type] $id [用户id]
 * @return
 */
function getUserInfo($id)
{
    return User::find($id)->toArray();
}

/**
 * [setPinneds 置顶数据组装].
 * @author Foreach
 * @param  [type] $data    [列表数据]
 * @param  [type] $pinneds [置顶数据]
 * @param  [type] $k       [键名]
 * @return [type]        [description]
 */
function formatPinneds($data, $pinneds)
{
    if (empty($pinneds)) {
        return $data;
    }
    $pinneds_keys = array_pluck($pinneds, 'id');
    foreach ($pinneds as $key => &$value) {
        $value['pinned'] = true;
    }
    foreach ($data as $key => &$value) {
        if (! in_array($value['id'], $pinneds_keys)) {
            $value['pinned'] = false;
            array_push($pinneds, $value);
        }
    }

    return $pinneds;
}

/**
 * [formatRepostable 转发数据组装].
 * @author Foreach
 * @param  [array] $feeds
 * @return [array]
 */
function formatRepostable($feeds)
{
    foreach ($feeds as &$feed) {
        if (! $feed['repostable_type']) {
            continue;
        }
        $feed['repostable'] = [];
        $id = $feed['repostable_id'];
        switch ($feed['repostable_type']) {
            case 'news':
                $feed['repostable'] = api('GET', "/api/v2/news/{$id}");
                break;
            case 'feeds':
                $feed_list = api('GET', '/api/v2/feeds', ['id' => $id.'']);
                if ($feed_list['feeds'][0] ?? false) {
                    $feed['repostable'] = $feed_list['feeds'][0];
                }
                break;
            case 'groups':
                $feed['repostable'] = api('GET', "/api/v2/plus-group/groups/{$id}");
                break;
            case 'group-posts':
            case 'posts':
                $post = api('GET', '/api/v2/group/simple-posts', ['id' => $id.'']);
                $feed['repostable'] = $post[0] ?? $post;
                if ($feed['repostable']['title'] ?? false) {
                    $feed['repostable']['group'] = api('GET', '/api/v2/plus-group/groups/'.$feed['repostable']['group_id']);
                }
                break;
            case 'questions':
                $feed['repostable'] = api('GET', "/api/v2/questions/{$id}");
                break;
            case 'question-answers':
                $feed['repostable'] = api('GET', "/api/v2/question-answers/{$id}");
                break;
        }
    }

    return $feeds;
}
