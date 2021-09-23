<?php
/*
 * ------------------------------------------------------------------
 * horseloft项目的定时任务配置文件
 * ------------------------------------------------------------------
 *
 * 当前配置文件不区分项目运行的环境变量；
 * 仅当 application.php 中的 crontab 字段全等于 true 时，定时执行当前文件中定义的任务
 */

return [
    'crontab_1' => [
        'command' => '* * * * *',
        'callback' => ['Application\Controller\DemoController', 'crontab1'],
        'args' => []
    ],
    'crontab_2' => [
        'command' => '*/2 * * * *',
        'callback' => ['\Application\Controller\DemoController', 'crontab2'],
        'args' => []
    ],
];