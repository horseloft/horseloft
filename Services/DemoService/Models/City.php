<?php
/**
 * Date: 2021/8/2 17:21
 * User: YHC
 * Desc:
 */

namespace Services\DemoService\Models;

use Library\Pdo\Table;

class City extends Table
{
    public static $table = 'city';

    public static $connection = 'main';
}