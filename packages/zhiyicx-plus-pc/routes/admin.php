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

use Illuminate\Support\Facades\Route;

Route::get('/', 'HomeController@show')->name('pc:admin');

// 认证
// Route::prefix('auth')->group(function () {
// 	Route::get('/', 'AuthUserController@getAuthUserList');
// 	Route::post('/audit/{aid}', 'AuthUserController@audit')->where(['aid'=>'[0-9+]']);
// 	Route::delete('/del/{$aid}/verified', 'AuthUserController@delAuthInfo')->where(['aid'=>'[0-9]+']);
// });

//举报
// Route::prefix('denounce')->group(function () {
// 	Route::get('/', 'DenounceController@getDenounceList');
// 	Route::post('/handle/{did}', 'DenounceController@handle')->where(['did'=>'[0-9]+']);
// });

//积分规则
// Route::prefix('credit')->group(function () {
// 	Route::get('/', 'CreditController@index');
// 	Route::post('/handle', 'CreditController@handleCreditRule');
// });

// 导航配置
Route::prefix('nav')->group(function () {
    Route::get('/list/{posit}', 'ConfigController@index')->where(['nid'=>'[0-9]+']);
    Route::get('/get/{nid}', 'ConfigController@getnav')->where(['nid'=>'[0-9]+']);
    Route::post('/manage', 'ConfigController@manage');
    Route::delete('/del/{nid}', 'ConfigController@delete')->where(['nid'=>'[0-9]+']);
});

// 基础配置
Route::prefix('site')->group(function () {
    Route::get('baseinfo', 'ConfigController@get');
    Route::patch('baseinfo', 'ConfigController@updateSiteInfo');
    Route::put('cacheclear', 'ConfigController@cacheclear');
});
