<?php

namespace Application\Controller;

use Application\Models\PeopleModel;
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
        return json_encode(['message' => 'index']);
    }

    public static function get()
    {
        return 'get';
    }

    public static function post()
    {
        return 'post';
    }

    public static function any()
    {
        return 'any';
    }

    public static function timer1()
    {
        echo PHP_EOL;
        echo 'timer1';
        echo PHP_EOL;
    }

    public static function timer5()
    {
        echo PHP_EOL;
        echo 'timer5';
        echo PHP_EOL;
    }

    public static function process1()
    {
        echo PHP_EOL;
        echo 'process10';
        echo PHP_EOL;
        sleep(10);
    }

    public static function process2()
    {
        echo PHP_EOL;
        echo 'process2';
        echo PHP_EOL;
        sleep(1);
    }

    public static function crontab1()
    {
        echo PHP_EOL;
        echo 'crontab1 ' . date('H:i:s');
        echo PHP_EOL;
    }

    public static function crontab2()
    {
        echo PHP_EOL;
        echo 'crontab2 ' . date('H:i:s');
        echo PHP_EOL;
    }

    public static function adapter()
    {
        return UserModel::select('id,username')->whereRaw('and id > 1')->whereOr(['id' => 5])->getSql();
    }

    public static function reservoir()
    {
        return PeopleModel::select('id,username')->whereRaw('and id > 1')->whereOr(['id' => 5])->all();
    }
}
