<?php
/**
 * Date: 2021/5/16 11:22
 * User: YHC
 * Desc:
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
        ],
        'know_test' => [
            'host' => '192.168.199.79',
            'port' => 3310,
            'database' => 'htolmain_new',
            'username' => 'htol_user',
            'password' => 'htwx_mysql@@102800',
            'driver' => 'mysql'
        ]
    ],
];
