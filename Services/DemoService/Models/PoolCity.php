<?php
/**
 * Date: 2021/8/2 18:39
 * User: YHC
 * Desc:
 */

namespace Services\DemoService\Models;

use Library\Pool\Table;

class PoolCity extends Table
{
    public static $table = 'city';

    public static $connection = 'main';
}