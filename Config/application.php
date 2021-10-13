<?php
/*
 * ------------------------------------------------------------------
 * horseloft项目的基础配置文件
 * ------------------------------------------------------------------
 *
 */

return [

    /**
     * 说明：服务监听的域名
     * 类型：IP
     */

    'server_host' => '0.0.0.0',

    /*
     * 说明：服务监听的端口
     * 类型：int
     */

    'server_port' => 10102,

    /*
     * 说明：自定义日志路径
     * 类型：string
     *
     * 值必须是绝对路径，如果值为空则使用项目目录下的Log目录作为日志目录
     */

    'log_path' => '',

    'response_success_code' => 200,
    'response_error_code' => 500,

    /*
     * 说明：是否开启debug
     * 类型：boolean
     * 默认值：true
     *
     * 值为 true，则不在请求响应中输出运行中的异常信息
     * 值为 false，则输出运行中的异常信息
     */

    'debug' => false,

    /*
     * 说明：是否启用默认路由
     * 类型：boolean
     * 默认值：true
     *
     * 值为 true 时，如果请求的路由不是用户自定义的路由，则尝试以pathInfo格式查找路由
     * 值为 false 时，仅请求用户自定义的路由
     */

    'default_route' => true,

    /*
     * 说明：是否开启毫秒定时器
     * 类型：boolean
     * 默认值：false
     *
     * 值为 true，则自动读取当前目录下的 time.php 文件的内容，并定时执行回调方法
     * 值为 false, 则不执行上述操作
     */

    'timer' => false,

    /*
     * 说明: 是否开启定时任务
     * 类型: boolean
     * 默认值：false
     *
     * 值为 true，则自动读取当前目录下的 crontab.php 文件的内容，并定时执行回调方法
     * 值为 false, 则不执行上述操作
     */

    'crontab' => false,

    /*
     * 说明: 是否开启自定义进程
     * 类型: boolean
     * 默认值：false
     *
     * 值为 true，则自动读取当前目录下的 process.php 文件的内容，并执行回调方法
     * 值为 false, 则不执行上述操作
     */

    'process' => false,

    /*
     * 说明：配置swoole启动的设置项
     * 类型：array
     * 默认值：空
     *
     * 不支持 log_file 项设置
     * 参考文档：https://wiki.swoole.com/#/server/setting
     */

    'swoole_set' => [
        'worker_num' => 4,
        'max_request' => 16,
        'task_worker_num' => 4,
        'task_max_request' => 8,
        'buffer_input_size' => 2 * 1024 * 1024, // 配置接收输入缓存区内存尺寸。【默认值：2M】
        'buffer_output_size' => 2 * 1024 * 1024, // 配置发送输出缓存区内存尺寸。【默认值：2M】
    ],
];