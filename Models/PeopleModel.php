<?php
/**
 * Date: 2021/10/11 14:11
 * User: YHC
 * Desc:
 */

namespace Application\Models;

use Horseloft\Database\Reservoir;

class PeopleModel extends Reservoir
{
    public static $table = 'people';

    public static $connection = 'default';
}
