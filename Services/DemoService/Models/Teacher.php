<?php
/**
 * Date: 2021/5/13 13:55
 * User: YHC
 * Desc:
 */

namespace Services\DemoService\Models;

use Horseloft\Database\Store;

class Teacher extends Store
{
    public static $table = 'teacher';

    public static $connection = 'default';
}