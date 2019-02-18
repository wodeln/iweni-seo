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

namespace Zhiyi\Plus\Services;

use Illuminate\Support\Collection;
use Zhiyi\Plus\Models\User as UserModel;
use Zhiyi\Plus\Contracts\Model\UserAbility as UserAbilityContract;

class UserAbility implements UserAbilityContract
{
    protected $user;

    /**
     * Get all roles or get first role.
     *
     * @param string $role
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function roles(string $role = '')
    {
        $roles = $this->user()
            ->roles()
            ->get()
            ->keyBy('name');

        if (! $role) {
            return $roles;
        }

        return $roles->get($role, false);
    }

    /**
     * Get all abilities or get first ability.
     *
     * @param string $ability
     * @return mixed
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function all(string $ability = '')
    {
        $roles = $this->roles();
        $roles->load('abilities');
        $abilities = $roles->reduce(function ($collect, $role) {
            return $collect->merge(
                $role->abilities->keyBy('name')
            );
        }, new Collection());

        if (! $ability) {
            return $abilities;
        }

        return $abilities->get($ability, false);
    }

    /**
     * Get user instance.
     *
     * @return \Zhiyi\Plus\Models\User
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function user(): UserModel
    {
        return $this->user;
    }

    /**
     * Set user model.
     *
     * @param \Zhiyi\Plus\Models\User $user
     * @author Seven Du <shiweidu@outlook.com>
     */
    public function setUser(UserModel $user)
    {
        $this->user = $user;

        return $this;
    }
}
