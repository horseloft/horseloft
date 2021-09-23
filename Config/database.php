<?php
/*
 * ------------------------------------------------------------------
 * horseloft项目的数据库配置文件
 * ------------------------------------------------------------------
 *
 * 配置文件区分项目运行的环境变量；例：dev,online,pro,test...
 */

return [
    'dev' => [
        'default' => [
            'host' => 'host.docker.internal',
            'port' => 3306,
            'database' => 'horseloft',
            'username' => 'root',
            'password' => '123456',
            'driver' => 'mysql'
        ],
        'main' => [
            'host' => '192.168.199.36',
            'port' => 11440,
            'database' => 'HTOLmain',
            'username' => 'htmain',
            'password' => 'z49yf^LLC,d,V9B)+q',
            'driver' => 'sqlserver'
        ]
    ],
];
