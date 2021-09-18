<?php
/**
 * Date: 2021/5/17 15:35
 * User: YHC
 * Desc:
 */

return [
    'dev' => [
        'main' => [
            'host' => 'host.docker.internal',
            'port' => 6379,
            'database' => 0,
            'password' => ''
        ]
    ],
    'online' => [
        'main' => [
            'host' => 'host.docker.internal',
            'port' => 6379,
            'database' => 1,
            'password' => ''
        ]
    ]
];
