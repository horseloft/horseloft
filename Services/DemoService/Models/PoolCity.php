<?php
/**
 * Date: 2021/8/2 18:39
 * User: YHC
 * Desc:
 */

namespace Services\DemoService\Models;

use Horseloft\Database\Store;

class PoolCity extends Store
{
    public static $table = 'city';

    public static $connection = 'main';
}