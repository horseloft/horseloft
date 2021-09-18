<?php
/**
 * Date: 2021/5/17 15:39
 * User: YHC
 * Desc:
 */

return [
    'timer5' => [
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
        'args' => ['name' => 'Tom'],

        /*
         * 定时调用指定的方法
         *
         * 例：DemoController::crontab
         * 则会调Controller/DemoController.php中的 crontab 方法
         */
        'callback' => ['\Application\Controller\DemoController', 'timer5']
    ],
    'timer1' => [
        'timer' => 1000,
        'args' => ['name' => 'Tom'],
        'callback' => ['\Application\Controller\DemoController', 'timer1']
    ]
];