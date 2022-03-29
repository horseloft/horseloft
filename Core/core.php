<?php

$loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$loader->addPsr4('Application' . '\\', dirname(__DIR__));

require_once dirname(__DIR__) . '/Core/functions.php';

/*
 * --------------------------------------------------------------------------
 * 数据容器 $_HORSELOFT_CORE_CONTAINER_ 变量名不能修改
 * --------------------------------------------------------------------------
 *
 */
$_HORSELOFT_CORE_CONTAINER_ = new \Horseloft\Core\Drawer\Building();
