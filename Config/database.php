<?php
/**
 * Date: 2021/5/16 11:22
 * User: YHC
 * Desc: 数据库配置信息
 */

return [
    /*
     * 自动读取当前环境下的数据库配置
     *
     * dev：
     *  如果服务启动的环境变量为dev【即启动命令：php start.php dev】，则自动读取dev下的数据库配置信息
     *
     */
    'dev' => [

        /*
         * default：
         *  在继承了 Library\Pool\Table 的类中，声明了$connection属性，并声明 $connection=default，
         *  则会自动读取当前环境变量下的default配置
         *
         */
        'default' => [
            'host' => 'host.docker.internal',
            'port' => 3306,
            'database' => 'horseloft',
            'username' => 'root',
            'password' => '123456',

            /*
             * driver:
             *  数据库驱动，当前服务仅支持MySQL驱动
             *
             */
            'driver' => 'mysql'
        ],

        /*
         * mysql：
         *  在继承了 Library\Pool\Table 的类中，声明了$connection属性，并声明 $connection=mysql，
         *  则会自动读取当前环境变量下的mysql配置
         *
         */
        'mysql' => [
            'host' => 'host.docker.internal',
            'port' => 3306,
            'database' => 'horseloft',
            'username' => 'root',
            'password' => '123456',
            'driver' => 'mysql'
        ]
    ],
    /*
     * 自动读取当前环境下的数据库配置
     *
     * online：如果服务启动的环境变量为online【即启动命令：php start.php online】，则自动读取online下的数据库配置信息
     *
     */
    'online' => [
        'default' => [
            'host' => 'host.docker.internal',
            'port' => 3306,
            'database' => 'horseloft',
            'username' => 'root',
            'password' => '123456',
            'driver' => 'mysql'
        ],
        'mysql' => [
            'host' => 'host.docker.internal',
            'port' => 3306,
            'database' => 'horseloft',
            'username' => 'root',
            'password' => '123456',
            'driver' => 'mysql'
        ]
    ]
];
