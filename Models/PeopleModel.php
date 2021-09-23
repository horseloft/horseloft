<?php

namespace Application\Models;

use Horseloft\Database\Reservoir;

/**
 * model继承连接池类
 *
 * Class PeopleModel
 * @package Application\Models
 */
class PeopleModel extends Reservoir
{
    public static $table = 'people';

    public static $connection = 'default';

    /**
     * 查询一条记录
     *
     * @return array
     */
    public static function getOnePeopleInfo()
    {
        return static::select()->first();
    }

    /**
     * 查询全部记录
     *
     * @return array
     */
    public static function getPeopleList()
    {
        return static::select('id,username,password')->all();
    }

    /**
     * 查询第一页数据 每页30条记录
     * @return array
     */
    public static function getPage()
    {
        return static::select('id,username,password')->page(1, 2)->all();
    }
}