<?php
/**
 * Date: 2021/4/25 17:29
 * User: YHC
 * Desc:
 */

//swoole启动参数
define('DEMO_SWOOLE_CONFIG', [
    'worker_num' => 4,
    'max_request' => 16,
    'task_worker_num' => 4,
    'task_max_request' => 8,
    'buffer_input_size' => 4 * 1024 * 1024,
    'buffer_output_size' => 4 * 1024 * 1024,
]);
