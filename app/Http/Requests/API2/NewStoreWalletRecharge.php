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

namespace Zhiyi\Plus\Http\Requests\API2;

use function Zhiyi\Plus\setting;
use Illuminate\Foundation\Http\FormRequest;

class NewStoreWalletRecharge extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function rules()
    {
        return [
            'type' => 'required|in:'.implode(',', setting('wallet', 'recharge-types', [])),
            'amount' => 'required|min:1|max:1000000',
            'extra' => 'array',
        ];
    }

    /**
     * Get the valodation error message that apply to the request.
     *
     * @return array
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function messages()
    {
        return [
            'type.required' => '请选择充值方式',
            'type.in' => '选择的充值方式错误',
            'amount.required' => '请选择需要充值金额',
            'amount.min' => '充值金额不合法',
            'amount.max' => '充值金额超出最大充值限制',
            'extra.array' => '参数错误',
        ];
    }
}
