<?php
/**
 * Date: 2021/6/1 10:26
 * User: YHC
 * Desc: 异常处理
 */

namespace Services\DemoService\Exceptions;

use Library\Core\Horseloft\Request;
use Library\Utils\Helper;

/**
 * 定义RuntimeException类 用于处理RuntimeException类型异常
 *
 * 当前类中必须存在handle()方法
 *
 * handle()方法必须有两个参数
 *
 * handle()方法的第一个参数：Library\Core\Horseloft\Request $request
 *
 * handle()方法的第二个参数：\Throwable $e
 *
 * 返回值如果是数组，并且含有 code data message 字段，则将这三个字段赋值给服务的返回值
 *
 * 返回值如果不是数组 或者 返回值中不存在 data 字段，则将本次异常处理的返回值赋值给 服务返回值的data字段
 *
 * 如果在当前类中出现了异常信息 则当前异常的处理无效，服务将使用默认的异常处理
 *
 * Class RuntimeException
 * @package Services\DemoService\Exceptions
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
            /*
             * 在此处定义返回码
             *
             * 如果没有code字段，则使用默认的全局错误码
             */
            'code' => Helper::errorCode(),

            /*
             * 定义接口返回数据
             *
             * 如果没有data字段 则将该方法的返回值作为data返回
             */
            'data' => $request->all(),

            /*
             * 定义接口返回的message
             *
             * 如果没有message字段 则默认返回''
             */
            'message' => '服务运行异常：' . $e->getMessage()
        ];
    }
}