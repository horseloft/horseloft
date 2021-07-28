<?php
/**
 * Date: 2021/4/25 17:00
 * User: YHC
 * Desc: HTTP服务
 */

namespace Library\Horseloft;

use Library\Core\Horseloft\Builder\EventBuilder;
use Library\Core\Horseloft\Builder\HttpEventBuilder;
use Library\Core\Horseloft\Builder\HttpRequestBuilder;
use Library\Core\Horseloft\Builder\RequestBuilder;
use Library\Core\Horseloft\Server;

class HTTPServer extends Server
{
    use HttpEventBuilder,EventBuilder,
        RequestBuilder,HttpRequestBuilder;

    /**
     * HTTPServer constructor.
     * @param string $swooleHost 监听IP
     * @param int $swoolePort 监听端口
     * @throws \Exception
     */
    public function __construct(string $swooleHost = '172.0.0.1', int $swoolePort = 10101)
    {
        parent::__construct($swooleHost, $swoolePort);

        $this->create();

        $this->onConnect();

        $this->onRequest();

        $this->onWorkerStart();

        $this->onClose();

        $this->onTask();

        $this->onFinish();
    }

    /**
     * --------------------------------------------------------------------------
     * 创建服务
     * --------------------------------------------------------------------------
     *
     */
    private function create()
    {
        try {
            $this->container()->setServer(
                new \Swoole\Http\Server($this->container()->getHost(), $this->container()->getPort())
            );
        } catch (\Exception $e) {
            exit('HTTP服务启动失败 ' . $e->getMessage());
        }
    }
}
