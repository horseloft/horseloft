<?php
/**
 * Date: 2021/10/22 16:46
 * User: YHC
 * Desc:
 */

namespace Application\Models;

use Horseloft\Database\Reservoir;

class MoreModel extends Reservoir
{
    public static $connection = 'more';

    public static $table = 'user';

}