<?php
/**
 * Date: 2021/10/11 17:14
 * User: YHC
 * Desc:
 */

namespace Application\Models;

use Horseloft\Database\Reservoir;

class CityModel extends Reservoir
{
    public static $connection = 'env';

    public static $table = 'city';

}
