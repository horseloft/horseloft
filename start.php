<?php

require_once __DIR__ . '/Core/core.php';

/*
 * --------------------------------------------------------------------------
 * 使用服务
 * --------------------------------------------------------------------------
 *
 * 不可更改变量名：$horseLoft
 * 变量 $horseLoft 将在全局被引用
 *
 * 第一个参数：服务监听的IP
 * 第二个参数：服务监听的端口
 *
 */
$horseLoft = new \Horseloft\Core\HTTPServer(__DIR__);





/*
 * --------------------------------------------------------------------------
 * 【启动
 * --------------------------------------------------------------------------
 *
 */
$horseLoft->start();
