<?php

namespace Application\Exceptions;

use Horseloft\Core\Drawer\Request;

/**
 * 定义RuntimeException类 用于处理 \RuntimeException() 类型异常
 *
 * 当前类中必须存在handle()方法
 *
 * handle()方法**必须**有两个参数
 *
 * handle()方法的第一个参数：Horseloft\Core\Drawer\Request $request
 *
 * handle()方法的第二个参数：\Throwable $e
 *
 * handle()方法的返回值将作为本次接口的响应值输出
 *
 * 如果在当前类中出现了异常信息 则当前异常的处理无效，服务将使用默认的异常处理
 *
 * Class RuntimeException
 * @package Application\Exceptions
 */
class RuntimeException
{
    /**
     * @param Request $request
     * @param \Throwable $e
     * @return array
     */
    public static function handle(Request $request, \Throwable $e)
    {
        return [
            'request' => $request->all(),
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ];
    }
}