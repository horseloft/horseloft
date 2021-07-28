<?php
/**
 * Date: 2021/5/17 16:50
 * User: YHC
 * Desc:
 */

return [
    /*
     * 路由前缀
     * 例：
     *  prefix => user; route => ['info/detail' => 'DemoController@empty']
     *  路由为：user/info/detail
     *
     * 配置路由前缀之后，当前路由都将加上该前缀
     *
     */
    'prefix' => '',

    /*
     * 控制器的命名空间
     *
     * 控制器的命名空间默认指向Controller,如果在控制器中新建了目录/命名空间，则需要在此处指明
     *
     * 例：
     *  Demo; 则指向了Controller\Demo命名空间
     *
     * 配置命名空间之后，当前route下的路由都将自动添加当前命名空间
     *
     */
    'namespace' => '',

    /*
     * 拦截器
     *
     * 拦截器指向Interceptor命名空间
     *
     * 此处拦截器名称 可以是小驼峰格式的命名；将指向 Interceptor命名空间 + 拦截器名称 + Interceptor.php
     *
     * 拦截器中必须存在方法handle,并且handle方法必须接受一个 Library\Core\Horseloft\Request 类型的参数
     *
     * 仅当handle方法返回值全等于TRUE时，拦截器验证通过，否则验证失败；验证失败时，将返回错误信息
     *
     * 例：
     *  default
     *  指向Interceptor命名空间下的 DefaultInterceptor.php
     *  并将自动执行 DefaultInterceptor.php 中的 handle(Request $request) 方法
     *  仅当 handle(Request $request) 方法 返回值 === true 时拦截器允许通过
     *
     */
    'interceptor' => 'default',

    /*
     * 路由
     *
     * key => value 格式
     *
     * key为路由
     * value为路由指向的类和类方法
     *
     */
    'route' => [
        'index' => 'DemoController@index',
        'empty' => 'DemoController@empty',
        'my/config' => 'DemoController@config',
        'all' => 'DemoController@all',
        'pdo' => 'DemoController@pdo',
    ]
];