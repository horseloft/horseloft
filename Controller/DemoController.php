<?php
/**
 * Date: 2021/4/25 16:57
 * User: YHC
 * Desc: demo
 */

namespace Application\Controller;

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
}
