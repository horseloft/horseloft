<?php

namespace Application\Controller;

use Application\Models\UserModel;

/**
 * ------------------------------------------------
 * 控制器中的方法必须是静态方法
 * ------------------------------------------------
 *
 * Class DemoController
 * @package Application\Controller
 */
class DemoController
{
    public static function index()
    {
        return 'horseloft';
    }

    public static function sql()
    {
        return UserModel::select()->first();
    }
}
