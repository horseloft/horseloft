<?php
/**
 * Date: 2021/5/18 09:42
 * User: YHC
 * Desc:
 */

namespace Library\Core\Horseloft;

class Container
{
    /**
     * @var \Swoole\Http\Server
     */
    private $server;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $port;

    /**
     * 控制器的命名空间
     *
     * @var string
     */
    private $namespace = HORSE_LOFT_CONTROLLER_NAMESPACE;

    /**
     * 日志路径
     *
     * @var string
     */
    private $logPath = HORSE_LOFT_SERVICE_LOG_PATH;

    /**
     * 日志文件名称
     *
     * @var string
     */
    private $logFilename = HORSE_LOFT_SERVICE;

    /**
     * crontab数据
     *
     * @var array
     */
    private $crontabConfig = [];

    /**
     * Redis队列配置数据，用于创建Redis订阅服务、消费队列
     *
     * @var array
     */
    private $redisQueueData = [];

    /**
     * Redis订阅服务的配置信息
     *
     * 以队列名称为key，Redis连接配置信息为value的数组
     *
     * @var array
     */
    private $redisQueueConfig = [];

    /**
     * swoole服务端配置项
     *
     * @var array
     */
    private $swooleConfig = [];

    /**
     * header
     *
     * @var array
     */
    private $requestHeader = [];

    /**
     * ip
     *
     * @var string
     */
    private $remoteAddr = '';

    /**
     * cookie
     *
     * @var array
     */
    private $requestCookie = [];

    /**
     * 上传的文件
     *
     * @var array
     */
    private $requestFiles = [];

    /**
     * @var array
     */
    private $params = [];

    /**
     * 响应
     *
     * @var \Swoole\Http\Response
     */
    private $response;

    /**
     * 响应的成功码
     *
     * @var int
     */
    private $successCode = HORSE_LOFT_SUCCESS_CODE;

    /**
     * 响应的失败码
     *
     * @var int
     */
    private $errorCode = HORSE_LOFT_ERROR_CODE;

    /**
     * 是否设置了全局错误码
     *
     * @var bool
     */
    private $globalErrorCode = false;

    /**
     * 配置信息
     *
     * @var array
     */
    private $configure = [];

    /**
     * 路由
     *
     * @var array
     */
    private $routeConfig = [];

    /**
     * 是否显示错误信息
     *
     * @var bool
     */
    private $globalErrorMessage = true;

    /**
     * 是否显示错误数据
     *
     * @var bool
     */
    private $globalErrorData = true;

    /**
     * @return array
     */
    public function getRedisQueueData(): array
    {
        return $this->redisQueueData;
    }

    /**
     * @param array $redisQueueData
     */
    public function setRedisQueueData(array $redisQueueData): void
    {
        array_push($this->redisQueueData, $redisQueueData);
    }

    /**
     * @return array
     */
    public function getRedisQueueConfig(): array
    {
        return $this->redisQueueConfig;
    }

    /**
     * @param string $channel
     * @param array $redisConfig
     */
    public function setRedisQueueConfig(string $channel, array $redisConfig): void
    {
        $this->redisQueueConfig[$channel] = $redisConfig;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * 向请求参数中 添加新的参数作为请求参数
     *
     * @param string $name
     * @param $value
     */
    public function addParam(string $name, $value): void
    {
        $this->params = array_merge($this->params, [$name => $value]);
    }

    /**
     * @param bool $globalErrorData
     */
    public function setGlobalErrorData(bool $globalErrorData): void
    {
        $this->globalErrorData = $globalErrorData;
    }

    /**
     * @return bool
     */
    public function isGlobalErrorData(): bool
    {
        return $this->globalErrorData;
    }

    /**
     * @param bool $globalErrorMessage
     */
    public function setGlobalErrorMessage(bool $globalErrorMessage): void
    {
        $this->globalErrorMessage = $globalErrorMessage;
    }

    /**
     * @return bool
     */
    public function isGlobalErrorMessage(): bool
    {
        return $this->globalErrorMessage;
    }

    /**
     * @param \Swoole\Http\Server $server
     */
    public function setServer(\Swoole\Http\Server $server): void
    {
        $this->server = $server;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @param string $port
     */
    public function setPort(string $port): void
    {
        $this->port = $port;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @param string $logPath
     */
    public function setLogPath(string $logPath): void
    {
        $this->logPath = $logPath;
    }

    /**
     * @param string $logFilename
     */
    public function setLogFilename(string $logFilename): void
    {
        $this->logFilename = $logFilename;
    }

    /**
     * @param array $crontabConfig
     */
    public function setCrontabConfig(array $crontabConfig): void
    {
        array_push($this->crontabConfig, $crontabConfig);
    }

    /**
     * @param array $swooleConfig
     */
    public function setSwooleConfig(array $swooleConfig): void
    {
        $this->swooleConfig = $swooleConfig;
    }

    /**
     * @param array $requestHeader
     */
    public function setRequestHeader(array $requestHeader): void
    {
        $this->requestHeader = $requestHeader;
    }

    /**
     * @param string $remoteAddr
     */
    public function setRemoteAddr(string $remoteAddr): void
    {
        $this->remoteAddr = $remoteAddr;
    }

    /**
     * @param array $requestCookie
     */
    public function setRequestCookie(array $requestCookie): void
    {
        $this->requestCookie = $requestCookie;
    }

    /**
     * @param array $requestFiles
     */
    public function setRequestFiles(array $requestFiles): void
    {
        $this->requestFiles = $requestFiles;
    }

    /**
     * @param \Swoole\Http\Response $response
     */
    public function setResponse(\Swoole\Http\Response $response): void
    {
        $this->response = $response;
    }

    /**
     * @param int $successCode
     */
    public function setSuccessCode(int $successCode): void
    {
        $this->successCode = $successCode;
    }

    /**
     * @param int $errorCode
     */
    public function setErrorCode(int $errorCode): void
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @param bool $globalErrorCode
     */
    public function setGlobalErrorCode(bool $globalErrorCode): void
    {
        $this->globalErrorCode = $globalErrorCode;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setConfigure($key, $value): void
    {
        $this->configure[$key] = $value;
    }

    /**
     *
     * @param string $key
     * @param $value
     */
    public function setRouteConfig(string $key, $value): void
    {
        $this->routeConfig[$key] = $value;
    }

    /*
     * ------------------------------------------------------------------
     *  GET
     * ------------------------------------------------------------------
     */

    /**
     * @return \Swoole\Http\Server
     */
    public function getServer(): \Swoole\Http\Server
    {
        return $this->server;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * @return string
     */
    public function getLogPath(): string
    {
        return $this->logPath;
    }

    /**
     * @return string
     */
    public function getLogFilename(): string
    {
        return $this->logFilename;
    }

    /**
     * @return array
     */
    public function getCrontabConfig(): array
    {
        return $this->crontabConfig;
    }

    /**
     * @return array
     */
    public function getSwooleConfig(): array
    {
        return $this->swooleConfig;
    }

    /**
     * @return array
     */
    public function getRequestHeader(): array
    {
        return $this->requestHeader;
    }

    /**
     * @return string
     */
    public function getRemoteAddr(): string
    {
        return $this->remoteAddr;
    }

    /**
     * @return array
     */
    public function getRequestCookie(): array
    {
        return $this->requestCookie;
    }

    /**
     * @return array
     */
    public function getRequestFiles(): array
    {
        return $this->requestFiles;
    }

    /**
     * @return \Swoole\Http\Response
     */
    public function getResponse(): \Swoole\Http\Response
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getSuccessCode(): int
    {
        return $this->successCode;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @return bool
     */
    /**
     * @return bool
     */
    public function isGlobalErrorCode(): bool
    {
        return $this->globalErrorCode;
    }

    /**
     * @return array
     */
    public function getConfigure(string $name = ''): array
    {
        if (empty($name)) {
            return $this->configure;
        }
        return isset($this->configure[$name]) ? $this->configure[$name] : [];
    }

    /**
     * @return array
     */
    public function getRouteConfig(): array
    {
        return $this->routeConfig;
    }
}
