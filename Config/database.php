<?php
/*
 * ------------------------------------------------------------------
 * horseloft项目的数据库配置文件
 * ------------------------------------------------------------------
 *
 */

return [
    'default' => [
        'host' => 'host.docker.internal',
        'port' => 3306,
        'database' => 'horseloft',
        'username' => 'root',
        'password' => '123456',
        'driver' => 'mysql'
    ],
    'more' => [
        'host' => 'host.docker.internal',
        'port' => 3306,
        'database' => 'horseloft',
        'username' => 'root',
        'password' => '123456',
        'driver' => 'mysql',
        'read' => [
            'host' => 'host.docker.internal',
            'port' => 3306,
            'database' => 'horseloft',
            'username' => 'root',
            'password' => '123456',
            'driver' => 'mysql',
            [
                'host' => 'host.docker.internal',
                'port' => 3306,
                'database' => 'horseloft',
                'username' => 'root',
                'password' => '123456',
                'driver' => 'mysql',
            ],
            [
                'host' => 'host.docker.internal',
                'port' => 3306,
                'database' => 'horseloft',
                'username' => 'root',
                'password' => '123456',
                'driver' => 'mysql',
            ]
        ],
        'write' => [
            'host' => 'host.docker.internal',
            'port' => 3307,
            'database' => 'horseloft',
            'username' => 'root',
            'password' => '123456',
            'driver' => 'mysql',
            [
                'host' => 'host.docker.internal',
                'port' => 3307,
                'database' => 'horseloft',
                'username' => 'root',
                'password' => '123456',
                'driver' => 'mysql',
            ],
            [
                'host' => 'host.docker.internal',
                'port' => 3307,
                'database' => 'horseloft',
                'username' => 'root',
                'password' => '123456',
                'driver' => 'mysql',
            ]
        ]
    ],
    'env' => env('database', [])
];
