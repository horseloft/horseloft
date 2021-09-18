<?php
/**
 * Date: 2021/9/15 14:15
 * User: YHC
 * Desc:
 */

return [
    /*
     * 1. 数组下标作为进程的名称
     *
     * 2. callback 自定义进程执行的回调方法
     *
     * 3. args 自定义进程执行的回调方法的参数
     */
    'first_process' => [
        'callback' => ['\Application\Controller\DemoController', 'process1'],
        'args' => []
    ],
    'second_process' => [
        'callback' => ['\Application\Controller\DemoController', 'process2'],
        'args' => []
    ]
];
