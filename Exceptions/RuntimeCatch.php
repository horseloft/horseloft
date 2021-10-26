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
class RuntimeCatch
{
    /**
     * @param Request $request
     * @param \Throwable $e
     * @return string
     */
    public static function handle(Request $request, \Throwable $e)
    {
        $class = (new \ReflectionClass($e))->getShortName();
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();
        $msg = $class . ' ERROR: ' . $message . ' in ' . $file . '(' . $line . ")\n" . $trace;
        return "自定义异常捕捉\n" . $msg . $request->getIP();
    }
}
