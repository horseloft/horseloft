<?php

namespace Application\Models;

use Horseloft\Database\Adapter;

/**
 * model继承普通数据库类
 *
 * Class UserModel
 * @package Application\Models
 */
class UserModel extends Adapter
{
    public static $table = 'user';

    public static $connection = 'default';

    /**
     * 查询一条数据
     *
     * @param int $id
     * @return array
     */
    public static function getOneUserInfoById(int $id)
    {
        return static::select()->where(['id' => $id])->first();
    }

    /**
     * 查询全部数据
     *
     * @return array
     */
    public static function getUserList()
    {
        return static::select('id,username,password')->all();
    }
}
