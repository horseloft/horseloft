<?php
/**
 * Date: 2021/5/22 11:10
 * User: YHC
 * Desc:
 */

namespace Library\Core\Horseloft\Builder;

use Library\Core\Horseloft\Spanner;

trait HttpEventBuilder
{
    /**
     * --------------------------------------------------------------------------
     * 建立连接事件
     * --------------------------------------------------------------------------
     *
     * onConnect/onClose 这 2 个回调发生在 Worker 进程内，而不是主进程
     * UDP 协议下只有 onReceive 事件，没有 onConnect/onClose 事件
     *
     */
    private function onConnect()
    {
        $this->container()->getServer()->on('connect', function (\Swoole\Server $server, int $fd, int $reactorId) {
            $this->connectTime = microtime(true);
        });
    }

    /**
     * --------------------------------------------------------------------------
     * 监听连接关闭事件
     * --------------------------------------------------------------------------
     *
     * onConnect/onClose 这 2 个回调发生在 Worker 进程内，而不是主进程
     * UDP 协议下只有 onReceive 事件，没有 onConnect/onClose 事件
     *
     */
    private function onClose()
    {
        $this->container()->getServer()->on('close', function (\Swoole\Server $server, int $fd, int $reactorId) {

            $costTime = (microtime(true) - $this->connectTime) * 1000;
            $runTime = round($costTime, 1);

            if ($runTime < 100) {
                $header = "\e[36m";
            } else if ($runTime > 250) {
                $header = "\e[31m";
            } else {
                $header = "\e[33m";
            }
            Spanner::cliPrint($header ."[" . $runTime . 'ms]' . "\e[0m");
        });
    }

    /**
     * --------------------------------------------------------------------------
     * 监听HTTP服务的数据接收
     * --------------------------------------------------------------------------
     *
     */
    private function onRequest()
    {
        $this->container()->getServer()->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
            try {
                //设置请求的开始时间
                $this->connectTime = microtime(true);

                //设置默认的输出类型为json；header为NGINX；header可以在程序中重新设置
                $response->header('Content-Type', 'application/json');
                $response->header('Server', 'Nginx');

                //设置HTTP响应码；可以在程序中重新设置
                $response->status(200);

                $code = $this->container()->getSuccessCode();
                $message = '';

                //ico请求
                if ($request->server['request_uri'] == '/favicon.ico') {
                    $response->end(Spanner::encode(['code' => $code, 'message' => $message, 'data' => '']));
                    //主动关闭本次连接
                    $this->container()->getServer()->close($response->fd);
                    return;
                }

                //request全局化
                $this->setRequestAdvance($request);

                //response全局化
                $this->setResponseAdvance($response);

                //请求数据整理
                $requestEncode = $this->requestEncode($request, ['ip' => $this->container()->getRemoteAddr()]);

                //输出请求信息到命令行
                Spanner::cliPrint($requestEncode);

                //task记录请求日志
                $this->container()->getServer()->task([
                    'function' => [\Library\Utils\Log::class, 'recording'],
                    'params' => [
                        $this->container()->getLogPath() . '/' . $this->container()->getLogFilename(),
                        $requestEncode
                    ]
                ]);

                //将请求参数置于容器
                $this->requestParamHandle($request);

                //路由 | 限流
                $route = $this->getRequestRoute($request);

                //拦截器
                $interceptor = $this->interceptorBuilder($route['interceptor']);
                if ($interceptor !== true) {
                    //数据返回至客户端
                    $response->end(Spanner::encode($interceptor));
                    //主动关闭本次连接
                    $this->container()->getServer()->close($response->fd);
                    return;
                }

                //有效参数
                $args = $this->getCallArgs($route['controller'], $route['function']);

                $returnData = call_user_func_array(
                    [
                        $this->container()->getNamespace() . $route['controller'],
                        $route['function']
                    ],
                    $args
                );
            } catch (\Throwable $e) {
                $exceptionHandle = $this->requestExceptionHandle($request, $e);
                $code = $exceptionHandle['code'];
                $returnData = $exceptionHandle['data'];
                $message = $exceptionHandle['message'];
            }

            //数据返回至客户端
            $response->end(Spanner::encode(['code' => $code, 'message' => $message, 'data' => $returnData]));
            //主动关闭本次连接
            $this->container()->getServer()->close($response->fd);
            return;
        });
    }
}
