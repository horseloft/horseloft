<?php
/*
 * --------------------------------------------------------------------------
 * 【读取env文件并定义当前运行环境】
 * --------------------------------------------------------------------------
 *
 */
if (!is_file(dirname(__DIR__) . '/env.php')) {
    die('env文件不存在');
}

require_once dirname(__DIR__) . '/env.php';

$loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$loader->addPsr4('Application' . '\\', dirname(__DIR__));

/*
 * --------------------------------------------------------------------------
 * 数据容器 $_HORSELOFT_CORE_CONTAINER_ 变量名不能修改
 * --------------------------------------------------------------------------
 *
 */
$_HORSELOFT_CORE_CONTAINER_ = new \Horseloft\Core\Drawer\Horseloft();
