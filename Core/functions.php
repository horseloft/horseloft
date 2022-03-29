<?php

use Horseloft\Core\Utils\Horseloft;

if (!function_exists('env')) {

    /**
     * 获取env.ini配置项的值
     *
     * @param string $name
     * @param mixed $default
     * @return false|mixed
     */
    function env(string $name = 'env', $default = false) {
        return Horseloft::env($name, $default);
    }
}

if (!function_exists('config')) {

    /**
     * --------------------------------------------------------------------------
     *  获取Config中配置的数据信息
     * --------------------------------------------------------------------------
     *
     * 如果未能读取到$name的配置信息，返回$default
     *
     * @param string $name
     * @param mixed $default
     * @return false|mixed
     */
    function config(string $name, $default = false) {
        return Horseloft::config($name, $default);
    }
}

if (!function_exists('task')) {

    /**
     * --------------------------------------------------------------------------
     * task
     * --------------------------------------------------------------------------
     *
     * 调用task执行一个异步任务
     *
     * $call 是完整命名空间的类名称及方法名称 例：[Library\Utils\HorseLoftUtil::class, 'encode']
     * $args 回调方法的参数 一个或者多个
     *
     * 返回值 false|task_id; task_id为0-n的int值
     *
     * @param callable $call
     * @param mixed ...$args
     * @return false|int
     */
    function task(callable $call, ...$args) {
        return Horseloft::task($call, $args);
    }
}
