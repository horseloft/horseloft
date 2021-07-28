<?php
/**
 * Date: 2021/5/13 13:55
 * User: YHC
 * Desc:
 */

namespace Services\DemoService\Models;

use Library\Pdo\Table;

class Teacher extends Table
{
    public static $table = 'teacher';

    public static $connection = 'default';
}