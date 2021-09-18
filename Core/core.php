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


$applicationDir = dirname(__DIR__);
/*
 * --------------------------------------------------------------------------
 * 【可选项】定义当前运行环境的常量
 * --------------------------------------------------------------------------
 *
 * 文件名称与APP_ENV定义的环境一致
 * 例：Config/production.php
 *
 */
if (is_file($applicationDir . '/Config/' .  APPLICATION_ENV . '.php')) {
    require_once $applicationDir . '/Config/' .  APPLICATION_ENV . '.php';
}

/*
 * --------------------------------------------------------------------------
 * 【可选项】加载通用配置文件
 * --------------------------------------------------------------------------
 *
 * 加载 HORSELOFT_CONFIG_DIR 目录下以Conf.php结尾的配置文件中的常量或变量
 * 目录路径 Config/*Conf.php
 *
 * 推荐：文件名以小写字母开头
 *
 */
if (is_dir($applicationDir . '/Config')) {
    $baseConfigHandle = opendir($applicationDir . '/Config');
    while (false !== $baseConfigFile = readdir($baseConfigHandle)) {
        if ($baseConfigFile == '.' || $baseConfigFile == '..') {
            continue;
        } else {
            $suffix = substr($baseConfigFile, -8);
            if ($suffix != false && $suffix == 'Conf.php') {
                require_once $applicationDir . '/Config/' . $baseConfigFile;
            }
        }
    }
    closedir($baseConfigHandle);
}

/*
 * --------------------------------------------------------------------------
 * 命名空间注册
 * --------------------------------------------------------------------------
 *
 *
 *
 */
if (!is_file($applicationDir . '/vendor/autoload.php')) {
    die('文件 vendor/autoload.php 不存在');
}
$loader = require_once $applicationDir . '/vendor/autoload.php';
$loader->addPsr4('Application' . '\\', $applicationDir);

/*
 * --------------------------------------------------------------------------
 * 数据容器 $_HORSELOFT_CORE_CONTAINER_ 变量名不能修改
 * --------------------------------------------------------------------------
 *
 */
$_HORSELOFT_CORE_CONTAINER_ = new \Horseloft\Core\Drawer\Horseloft();
