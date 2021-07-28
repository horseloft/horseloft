<?php
/**
 * Date: 2021/5/17 15:39
 * User: YHC
 * Desc:
 */

return [
    'demo' => [

        /*
         * 单位：毫秒
         *
         * 每间隔 timer毫秒 执行一次
         */
        'timer' => 5000,


        /*
         * 向被调函数传的参数
         *
         * 不支持同时传多个参数
         *
         * 如果传递多个参数，请使用数组传递
         */
        'params' => ['name' => 'Tom'],


        /*
         * 默认命名空间指向 \Services\DemoService
         *
         * 如果调用Controller/下的方法，需要配置namespace为 Controller，Controller前后不需要使用斜线
         */
        'namespace' => 'Controller',


        /*
         * 定时调用指定的方法
         *
         * 例：DemoController@crontabEcho
         * 则会调/Services/DemoService/Controller/DemoController.php中的crontabEcho方法
         */
        'callback' => 'DemoController@crontabEcho'
    ]
];