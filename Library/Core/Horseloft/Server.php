<?php
/**
 * Date: 2021/4/25 11:00
 * User: YHC
 * Desc:
 */

namespace Library\Core\Horseloft;

use Library\Core\container;
use Library\Core\Horseloft\Builder\PluginBuilder;

class Server
{
    use container,PluginBuilder;

    protected $connectTime;

    /**
     * Server constructor.
     * @param string $host
     * @param int $port
     * @throws \Exception
     */
    public function __construct(string $host, int $port)
    {
        if (version_compare(phpversion(), '7.1.0', '<')) {
            exit('PHP-版本不能低于7.1.0');
        }
        if (!extension_loaded('swoole')) {
            exit('PHP-Swoole扩展不存在');
        }
        if (floatval(phpversion('swoole')) < 4.4) {
            exit('PHP-Swoole扩展的版本不能低于4.4');
        }
        if (empty($host)) {
            throw new \Exception('error host');
        }
        if ($port <= 0) {
            throw new \Exception('error port');
        }

        $this->container()->setHost($host);
        $this->container()->setPort($port);
    }

    /**
     * --------------------------------------------------------------------------
     * 启动服务器
     * --------------------------------------------------------------------------
     *
     */
    final public function start()
    {
        //设置配置项
        $this->container()->getServer()->set($this->container()->getSwooleConfig());

        //创建定时任务
        $this->createCrontab();

        //创建队列
        $this->createRedisQueue();

        //加载配置 | 数据库
        $this->setDatabaseConfigure();

        //加载配置 | 拦截器
        $this->setInterceptorConfigure();

        //服务信息展示
        Spanner::cliPrint(APP_ENV . ' services start -> ' . $this->container()->getHost() . ':' . $this->container()->getPort());

        //Swoole启动
        if (!$this->container()->getServer()->start()) {
            exit('server start fail');
        }
    }

    /**
     * --------------------------------------------------------------------------
     * 服务启动的配置项
     * --------------------------------------------------------------------------
     *
     * @param array $config
     * @return bool
     */
    final public function setSwooleConfig(array $config = [])
    {
        $default = [
            //是否后台运行
            'daemonize' => false,
            //错误日志
            'log_file' => $this->container()->getLogPath() . '/' . strtolower($this->container()->getLogFilename()) . '.log'
        ];
        $this->container()->setSwooleConfig(array_merge($default, $config));
        return true;
    }

    /**
     * --------------------------------------------------------------------------
     * 设置服务运行日志路径
     * --------------------------------------------------------------------------
     *
     * @param string $path
     * @return bool
     */
    final public function setLogPath(string $path)
    {
        if (!is_dir($path)) {
            //递归创建目录
            if (!is_dir($path)) {
                $old = umask(0);
                if (!mkdir($path, 0777, true)) {
                    exit('error log path' . PHP_EOL);
                }
                umask($old);
            }
        }
        $this->container()->setLogPath(rtrim($path, '/'));
        return true;
    }

    /**
     * --------------------------------------------------------------------------
     * 设置日志文件名
     * --------------------------------------------------------------------------
     *
     * $filename 不包含文件路径，仅是文件名称；不应设置文件名称的后缀
     * 当前方法自动追加文件名称后缀为.log
     *
     * @param string $filename
     * @return bool
     */
    final public function setLogFilename(string $filename) {
        if (strlen($filename) > 0) {
            $this->container()->setLogFilename($filename);
        }
        return true;
    }

    /**
     * --------------------------------------------------------------------------
     * 设置一个定时任务
     * --------------------------------------------------------------------------
     *
     * @param array $config
     *
     * $config = [
     *      'timer' => 1000,                                // 毫秒
     *      'namespace' => 'Controller',                    // 命名空间
     *      'callback' => 'DemoController@myFunction',      // 回调
     *      'params' => 123                                 // 仅支一个参数|int string bool array
     *  ]
     *
     * @return bool
     */
    final public function setCrontab(array $config)
    {
        if (empty($config)) {
            return true;
        }
        if (empty($config['timer']) || !is_int($config['timer']) || $config <= 0) {
            exit('Unsupported timer' . PHP_EOL);
        }

        $config['callback'] = $this->getCallback($config);
        if ($config['callback'] == false) {
            exit('callback errors；Example：DemoController@myFunction' . PHP_EOL);
        }

        $this->container()->setCrontabConfig($config);
        return true;
    }

    /**
     * --------------------------------------------------------------------------
     * 设置Redis队列配置
     * --------------------------------------------------------------------------
     *
     * @param string $channel
     * @param array $config
     * $config = [
     *      'host' => 'localhost',
     *      'port' => 6381
     *      'database' => 1,
     *      'password' => ''
     * ]
     * @return bool
     */
    final public function setRedisQueue(string $channel, array $config)
    {
        if (empty($channel)) {
            exit('empty channel' . PHP_EOL);
        }
        if (empty($config)) {
            exit('empty redis config' . PHP_EOL);
        }
        if (empty($config['host'])) {
            exit('empty host in redis config' . PHP_EOL);
        }
        if (empty($config['port'])) {
            exit('empty port in redis config' . PHP_EOL);
        }

        $this->container()->setRedisQueueData(['channel' => $channel, 'config' => $config]);

        $this->container()->setRedisQueueConfig($channel, $config);

        return true;
    }

    /**
     * --------------------------------------------------------------------------
     *  声明需要加载的配置文件的名称
     * --------------------------------------------------------------------------
     *
     * $name = 'redis' //读取Config目录中的redis.php文件
     *
     * $name = 'user' //读取Config目录中的user.php文件
     *
     * $name值应为字符串格式
     *
     * @param string $name
     * @return bool
     */
    final public function setConfigure(string ...$name)
    {
        //加载全局配置信息
        $this->readSetConfigure($name, HORSE_LOFT_CONFIG_PATH);

        //加载项目配置信息
        $this->readSetConfigure($name, HORSE_LOFT_SERVICE_PATH . '/Config');

        return true;
    }

    /**
     * --------------------------------------------------------------------------
     *  加载路由文件
     * --------------------------------------------------------------------------
     *
     * @param string ...$name
     * @return bool
     */
    final public function setRoute(string ...$name)
    {
        return $this->readSetRoute($name);
    }

    /**
     * --------------------------------------------------------------------------
     *  请求成功返回的code值
     * --------------------------------------------------------------------------
     *
     * @param int $code
     * @return bool
     */
    final public function setServiceSuccessCode(int $code = 10200)
    {
        $this->container()->setSuccessCode($code);

        return true;
    }

    /**
     * --------------------------------------------------------------------------
     *  请求失败返回的code值
     * --------------------------------------------------------------------------
     *
     * 默认使用内部错误码
     *
     * 如果没有设置错误码，将输出服务内部错误码；
     * Services中的异常如果未设置错误码，则使用默认错误码：500，如果设置了错误码，则使用设置的错误码
     *
     * 如果设置了错误码，那么所有的错误响应都使用$code
     *
     * @param int $code
     * @return bool
     */
    final public function setServiceErrorCode(int $code = 10500)
    {
        $this->container()->setErrorCode($code);

        $this->container()->setGlobalErrorCode(true);

        return true;
    }

    /**
     * --------------------------------------------------------------------------
     *  请求失败时是否显示失败的提示信息
     * --------------------------------------------------------------------------
     *
     * 默认显示错误信息
     *
     * @param bool $isShow
     * @return bool
     */
    final public function setShowErrorMessageDetail(bool $isShow = true)
    {
        $this->container()->setGlobalErrorMessage($isShow);

        return true;
    }

    /**
     * --------------------------------------------------------------------------
     *  请求失败时是否显示失败的详细数据
     * --------------------------------------------------------------------------
     *
     * 默认显示错误数据
     *
     * @param bool $isShow
     * @return bool
     */
    final public function setShowErrorDataDetail(bool $isShow = true)
    {
        $this->container()->setGlobalErrorData($isShow);

        return true;
    }
}
