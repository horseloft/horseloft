<?php
/**
 * Date: 2021/4/29 09:38
 * User: YHC
 * Desc: 服务的全局数据的读取、设置
 */

namespace Library\Utils;

use Library\Core\Horseloft\Container;

class Helper
{
    /**
     * horseloft service object
     * @return Container
     */
    private static function horseloft()
    {
        if (!isset($GLOBALS[HORSE_LOFT_CONTAINER])) {
            throw new \RuntimeException('missing container', HORSE_LOFT_OTHER_ERROR_CODE);
        }
        return $GLOBALS[HORSE_LOFT_CONTAINER];
    }

    /**
     * 服务的响应
     * @return \Swoole\Http\Response
     */
    private static function response()
    {
        $response = self::horseloft()->getResponse();
        if (is_null($response) || !($response instanceof \Swoole\Http\Response)) {
            throw new \RuntimeException('container response is null', HORSE_LOFT_OTHER_ERROR_CODE);
        }

        return $response;
    }

    /**
     * 读取配置信息
     * @param string $name
     * @param bool $isEnv
     * @param false $default
     * @return array|false|mixed
     */
    private static function getConfig(string $name, bool $isEnv, $default = false)
    {
        if (strlen($name) == 0) {
            return $default;
        }
        $list = explode('.', $name);

        if ($isEnv) {
            array_splice($list, 1, 0, APP_ENV);
        }

        $var = self::horseloft()->getConfigure();
        foreach ($list as $value) {

            if (!isset($var[$value])) {
                return $default;
            }
            $var = $var[$value];
        }

        return $var;
    }

    /**
     * --------------------------------------------------------------------------
     *  获取Config中配置的数据信息
     * --------------------------------------------------------------------------
     *
     * 如果未能读取到$name的配置信息，返回$default
     *
     * @param string $name
     * @param false $default
     * @return array|false|mixed
     */
    public static function config(string $name, $default = false)
    {
        return self::getConfig($name, false, $default);
    }

    /**
     * --------------------------------------------------------------------------
     *  获取Config中配置的数据信息
     * --------------------------------------------------------------------------
     *
     * 自动读取当前环境变量 并获取配置信息
     *
     * 如果未能读取到$name的配置信息，返回$default
     *
     * 假如存在配置:redis.dev.demo，并且当前环境变量:APP_ENV=dev
     * 那么：$name = redis.demo
     * 即可获取redis.dev.demo的配置信息
     *
     * @param string $name
     * @param false $default
     * @return array|false|mixed
     */
    public static function envConfig(string $name, $default = false)
    {
        return self::getConfig($name, true, $default);
    }

    /**
     * --------------------------------------------------------------------------
     * task
     * --------------------------------------------------------------------------
     *
     * 调用task执行一个异步任务
     *
     * $call 是完整命名空间的类名称及方法名称 例：[Library\Utils\HorseLoftUtil::class, 'encode']
     * $args 回调方法的参数 一个或者多个
     *
     * 返回值 false|task_id; task_id为0-n的int值
     *
     * @param callable $call
     * @param mixed ...$args
     * @return false|int
     */
    public static function task(callable $call, ...$args)
    {
        return self::horseloft()->getServer()->task(
            [
                'function' => $call,
                'params' => $args
            ]
        );
    }

    /**
     * --------------------------------------------------------------------------
     * 使用task异步写日志
     * --------------------------------------------------------------------------
     *
     * $filename为空 则使用默认日志文件
     *
     * @param $message
     * @param string $filename
     * @return false|int
     */
    public static function log($message, string $filename = '')
    {
        $horseloft = self::horseloft();

        if (empty($filename)) {
            $filename = $horseloft->getLogFilename();
        }

        return $horseloft->getServer()->task(
            [
                'function' => [\Library\Utils\Log::class, 'recording'],
                'params' => [
                    $horseloft->getLogPath() . '/' . $filename,
                    $message
                ]
            ]
        );
    }

    /**
     * --------------------------------------------------------------------------
     * 设置header
     * --------------------------------------------------------------------------
     *
     * @param string $name
     * @param string $value
     * @return bool
     */
    public static function setHeader(string $name, string $value)
    {
        if (strlen(trim($value)) == 0) {
            return false;
        }

        if (self::response()->header($name, $value) === false) {
            return false;
        }
        return true;
    }

    /**
     * --------------------------------------------------------------------------
     * 设置header
     * --------------------------------------------------------------------------
     *
     * $header: 以header名为key，header值为value的一维数组
     *
     * @param array $header
     * @return bool
     */
    public static function setHeaders(array $header)
    {
        if (empty($header)) {
            return false;
        }

        $response = self::response();

        foreach ($header as $key => $value) {
            if ($response->header($key, $value) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * --------------------------------------------------------------------------
     * 设置cookie; 会自动会对 cookie 进行 urlencode 编码
     * --------------------------------------------------------------------------
     *
     * Swoole 会自动会对 $value 进行 urlencode 编码
     *
     * 可使用 rawCookie() 方法关闭对 $value 的编码处理
     *
     * Swoole 允许设置多个相同 $name 的 COOKIE
     *
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @return bool
     */
    public static function setCookie(string $name, string $value = '', int $expire = 0, string $path = '/', string $domain = '')
    {
        if (strlen(trim($name)) == 0) {
            return false;
        }
        if (self::response()->cookie($name, $value, $expire, $path, $domain) === false) {
            return false;
        }
        return true;
    }

    /**
     * --------------------------------------------------------------------------
     * 设置cookie
     * --------------------------------------------------------------------------
     *
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @return bool
     */
    public static function setRawCookie(string $name, string $value = '', int $expire = 0, string $path = '/', string $domain = '')
    {
        if (strlen(trim($name)) == 0) {
            return false;
        }
        if (self::response()->rawCookie($name, $value, $expire, $path, $domain) === false) {
            return false;
        }
        return true;
    }

    /**
     * --------------------------------------------------------------------------
     * 获取header值
     * --------------------------------------------------------------------------
     *
     * @param string $name
     * @return string
     */
    public static function getHeader(string $name)
    {
        if (strlen(trim($name)) == 0) {
            return '';
        }
        return isset(self::getCompleteHeader()[$name]) ? self::getCompleteHeader()[$name] : '';
    }

    /**
     * --------------------------------------------------------------------------
     * 获取全部header
     * --------------------------------------------------------------------------
     *
     * @return array
     */
    public static function getCompleteHeader()
    {
        return self::horseloft()->getRequestHeader();
    }

    /**
     * 获取cookie
     *
     * @param string $name
     * @return string
     */
    public static function getCookie(string $name)
    {
        if (strlen(trim($name)) == 0) {
            return '';
        }
        return isset(self::getCompleteCookie()[$name]) ? self::getCompleteCookie()[$name] : '';
    }

    /**
     * 获取全部cookie
     *
     * @return array
     */
    public static function getCompleteCookie()
    {
        return self::horseloft()->getRequestCookie();
    }

    /**
     * 向请求参数中添加新的参数作为请求参数的一部分
     *
     * 注：
     *  1. 在拦截器使用该方法，则添加的参数将作为请求参数的一部分传递给路由方法
     *  2. 在拦截器之外（Controller,Service等）使用该方法，则只能使用Helper::getRequest()或Helper::getCompleteRequest()获取
     *  3. 使用该方法添加的参数，将替换请求参数中已有的同名参数的值
     *
     * @param string $name
     * @param $value
     */
    public static function setRequest(string $name, $value)
    {
        self::horseloft()->addParam($name, $value);
    }

    /**
     * 获取指定的请求参数值
     *
     * @param string $name
     * @return mixed|string
     */
    public static function getRequest(string $name)
    {
        if (strlen(trim($name)) == 0) {
            return '';
        }
        $params = self::horseloft()->getParams();
        if (isset($params[$name])) {
            return  $params[$name];
        }
        return '';
    }

    /**
     * 获取全部请求参数和值
     *
     * @return array
     */
    public static function getCompleteRequest()
    {
        return self::horseloft()->getParams();
    }

    /**
     * 通过Redis队列名称 获取Redis的连接配置信息
     *
     * @param string $channel
     * @return array
     */
    public static function getQueueRedisConfig(string $channel)
    {
        $configData = self::horseloft()->getRedisQueueConfig();
        if (empty($configData)) {
            return [];
        }
        if (isset($configData[$channel])) {
            return $configData[$channel];
        }
        return [];
    }

    /**
     * 获取HTTP请求失败时的错误码
     *
     * @return int
     */
    public static function errorCode()
    {
        return self::horseloft()->getErrorCode();
    }

    /**
     * 获取HTTP请求失败时的状态码
     *
     * @return int
     */
    public static function successCode()
    {
        return self::horseloft()->getSuccessCode();
    }


    /**
     *  获取服务的日志存储路径
     *
     * @return string
     */
    public static function logPath()
    {
        return self::horseloft()->getLogPath();
    }

    /**
     * 获取定时任务配置信息
     *
     * @return array
     */
    public static function crontab()
    {
        return self::horseloft()->getCrontabConfig();
    }

    /**
     * 获取Redis队列配置信息
     *
     * @return array
     */
    public static function redisQueue()
    {
        return self::horseloft()->getRedisQueueData();
    }

    /**
     * 获取swoole的启动配置项
     *
     * @return array
     */
    public static function swooleConfig()
    {
        return self::horseloft()->getSwooleConfig();
    }

    /**
     * 获取请求IP
     *
     * @return string
     */
    public static function remoteAddr()
    {
        return self::horseloft()->getRemoteAddr();
    }

    /**
     * 获取上传的文件 | 返回值：二维数组
     *
     * @return array
     */
    public static function files()
    {
        return self::horseloft()->getRequestFiles();
    }

    /**
     * 获取自定义路由配置信息
     *
     * @return array
     */
    public static function route()
    {
        return self::horseloft()->getRouteConfig();
    }
}
