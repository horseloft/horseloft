<?php
/*
 * ------------------------------------------------------------------
 * horseloft项目的毫秒定时任务配置文件
 * ------------------------------------------------------------------
 *
 * 当前配置文件不区分项目运行的环境变量；
 * 仅当 application.php 中的 timer 字段全等于 true 时，定时执行当前文件中定义的任务
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