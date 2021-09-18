<?php
/**
 * Date: 2021/9/15 14:37
 * User: YHC
 * Desc:
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