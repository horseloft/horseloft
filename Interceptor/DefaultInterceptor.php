<?php
/**
 * Date: 2021/5/28 17:10
 * User: YHC
 * Desc: 拦截器demo
 */

namespace Application\Interceptor;

use Horseloft\Core\Drawer\Request;

class DefaultInterceptor
{
    /**
     * 拦截器必须有方法handle 并且handle必须制定参数$request 并且格式为Request
     *
     * 仅当返回值===true时，允许请求通过拦截器
     *
     * 如果拦截器返回值不全等于 true, 并且返回值中有 code/message/data 则替换默认的；如果没有 则将返回的信息赋值给data
     *
     * @param Request $request
     * @return bool
     */
    public static function handle(Request $request)
    {
        return true;
    }
}