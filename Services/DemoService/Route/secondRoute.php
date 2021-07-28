<?php
/**
 * Date: 2021/5/17 16:38
 * User: YHC
 * Desc:
 */

return [
    'prefix' => 'v1',     // 路由前缀
    'namespace' => 'Demo', // 控制器的命名空间
    'interceptor' => '',  // 拦截器
    'route' => [
        'next' => 'NextController@next', // 路由：user/next
        'exception' => 'NextController@exception',
    ]
];
