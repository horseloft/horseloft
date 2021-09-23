<?php
/*
 * --------------------------------------------------------------------------
 * 【定义当前运行环境】 $argv
 * --------------------------------------------------------------------------
 *
 * 用于定义当前环境的Service常量
 * APP_ENV：
 * 当前项目环境；参考值：dev|test|production|online
 *
 */
if (empty($argv) || empty($argv[1])) {
    die('启动命令错误；参考格式：php index.php dev' . PHP_EOL);
}
if (is_numeric($argv[1])) {
    die('启动命令错误；不能使用数字作为服务运行环境' . PHP_EOL);
}
define('APPLICATION_ENV', $argv[1]);


$loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$loader->addPsr4('Application' . '\\', dirname(__DIR__));

/*
 * --------------------------------------------------------------------------
 * 数据容器 $_HORSELOFT_CORE_CONTAINER_ 变量名不能修改
 * --------------------------------------------------------------------------
 *
 */
$_HORSELOFT_CORE_CONTAINER_ = new \Horseloft\Core\Drawer\Horseloft();
