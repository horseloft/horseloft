<?php
/**
 * Date: 2021/5/22 11:55
 * User: YHC
 * Desc:
 */

namespace Library\Core\Horseloft\Builder;

use Library\Core\Horseloft\Request;
use Library\Core\Horseloft\Spanner;

trait RequestBuilder
{
    /**
     * --------------------------------------------------------------------------
     * 请求数据全局化|request
     * --------------------------------------------------------------------------
     *
     * @param \Swoole\Http\Request $request
     */
    private function setRequestAdvance(\Swoole\Http\Request $request)
    {
        $this->container()->setRequestHeader($request->header);

        if (isset($request->header['x-forwarded-for'])) {
            $remoteAddr = $request->header['x-forwarded-for'];
        } else if (isset($request->header['x-real-ip'])) {
            $remoteAddr = $request->header['x-real-ip'];
        } else {
            $remoteAddr = $request->server['remote_addr'];
        }
        //如果是代理转发，IP为逗号分隔的字符串
        if (strpos($remoteAddr, ',')) {
            $addr = explode(',', $remoteAddr);
            $remoteAddr = end($addr);
        }
        $this->container()->setRemoteAddr($remoteAddr);

        $this->container()->setRequestCookie(empty($request->cookie) ? [] : $request->cookie);

        $this->container()->setRequestFiles(empty($request->files) ? [] : $request->files);
    }

    /**
     * --------------------------------------------------------------------------
     * 响应数据全局化|request
     * --------------------------------------------------------------------------
     *
     * @param $response
     */
    private function setResponseAdvance(\Swoole\Http\Response $response)
    {
        $this->container()->setResponse($response);
    }

    /**
     * 请求数据整理
     * @param \Swoole\Http\Request $request
     * @param array $message
     * @param string $logType
     * @return string
     */
    private static function requestEncode(\Swoole\Http\Request $request, array $message, string $level = 'info')
    {
        $log = [
            'get' => $request->get,
            'post' => $request->post,
            'rawContent' => null,
            'cookie' => $request->cookie,
            'file' => $request->files
        ];
        $raw = null;
        if (is_null($log['get']) && is_null($log['post'])) {
            $log['rawContent'] = @$request->rawContent();
        }
        $date =(new \DateTime)->format('Y-m-d H:i:s:u');

        $json = ltrim(Spanner::encode(array_merge($log, $message)), '{');

        //解决json_encode后url斜线转义的替代方案
        return <<<horse_loft_flag
{"level":"{$level}","date":"{$date}","method":"{$request->server['request_method']}","uri":"{$request->server['request_uri']}",$json
horse_loft_flag;
    }

    /**
     *
     * @param \Swoole\Http\Request $request
     * @param \Throwable $e
     * @return array
     */
    private function requestExceptionHandle(\Swoole\Http\Request $request, \Throwable $e)
    {
        //输出错误信息到命令行
        Spanner::cliExceptionPrint($e);

        $code = $e->getCode();
        $message = $e->getMessage();

        $returnData = [
            'message' => $message,
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $code
        ];


        /**
         * 如果在Exceptions下存在与异常名称相同的类 则使用该类处理异常，该类需要满足以下条件：
         *  1：类中存在handle(Request $request, \Throwable $e)方法
         *  2：handle()方法支持两个参数：
         *   第一个参数：Request $request
         *   第二个参数：Library\Core\Horseloft\Request\Throwable $e
         *  3：如果类中出现异常 则使用默认处理
         *
         * 如果返回值中有 code/message/data 则替换默认的；如果没有 则将返回的信息赋值给data，
         *
         * 如果没有自定义的异常处理 则使用默认处理
         *
         */
        $reflection = new \ReflectionClass($e);
        $namespace = rtrim(rtrim(HORSE_LOFT_CONTROLLER_NAMESPACE, '\\'), 'Controller') . 'Exceptions\\';
        if (is_callable([$namespace . $reflection->getName(), 'handle'])) {
            try {
                $callback = call_user_func_array([$namespace . $reflection->getName(), 'handle'], [new Request(), $e]);
                return [
                    'code' => isset($callback['code']) ? $callback['code'] : $this->container()->getErrorCode(),
                    'data' => isset($callback['data']) ? $callback['data'] : $callback,
                    'message' => isset($callback['message']) ? $callback['message'] : ''
                ];
            } catch (\Throwable $e){
                //有异常 则使用默认处理
            }
        }

        //Task记录错误日志
        $this->container()->getServer()->task([
            'function' => [\Library\Utils\Log::class, 'recording'],
            'params' => [
                $this->container()->getLogPath() . '/' . $this->container()->getLogFilename(),
                $this->requestEncode($request, $returnData, 'error')
            ]
        ]);

        //是否输出错误信息
        if ($this->container()->isGlobalErrorMessage() == false) {
            $message = '服务异常';
        }

        //是否输出错误数据
        if ($this->container()->isGlobalErrorData() == false) {
            $returnData = '';
        }

        // 是否使用全局错误码
        if ($this->container()->isGlobalErrorCode()) {
            $code = $this->container()->getErrorCode();
        } else if ($code == 0) {
            $code = $this->container()->getErrorCode();
        }

        return [
            'code' => $code,
            'message' => $message,
            'data' => $returnData
        ];
    }
}