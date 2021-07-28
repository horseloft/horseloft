<?php
/**
 * Date: 2021/5/13 13:53
 * User: YHC
 * Desc:
 */

namespace Services\DemoService\Models;

use Library\Pool\Table;

class People extends Table
{
    public static $table = 'people';

    public static $connection = 'default';

    /**
     * 查询一条记录
     * @return array
     */
    public static function getOne()
    {
        return static::select()->first();
    }

    /**
     * 查询第一页数据 每页30条记录
     * @return array
     */
    public static function getPage()
    {
        return static::select('id,username,password')->where(['type' => 1])->page(1, 30)->all();
    }
}