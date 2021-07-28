<?php
/**
 * Date: 2021/5/22 11:28
 * User: YHC
 * Desc: 附属功能
 */

namespace Library\Core\Horseloft\Builder;

use Library\Core\Horseloft\Server;

trait PluginBuilder
{
    /**
     * --------------------------------------------------------------------------
     *  创建定时任务
     * --------------------------------------------------------------------------
     *
     * @param \Swoole\Http\Server $server
     * @param Server $horseLoft
     */
    protected function createCrontab()
    {
        if (empty($this->container()->getCrontabConfig())) {
            return;
        }

        $process = new \Swoole\Process(function () {
            if ("Darwin" != PHP_OS) {
                swoole_set_process_name('crontab_process');
            }
            foreach ($this->container()->getCrontabConfig() as $value) :
                if (!is_array($value)) {
                    continue;
                }

                \Swoole\Timer::tick($value['timer'], function() use($value) {
                    if (isset($value['params'])) {
                        call_user_func_array($value['callback'], [$value['params']]);
                    } else {
                        call_user_func_array($value['callback'], []);
                    }
                });

            endforeach;
        });
        $this->container()->getServer()->addProcess($process);
    }

    /**
     * --------------------------------------------------------------------------
     * 创建Redis队列
     * --------------------------------------------------------------------------
     *
     * @param \Swoole\Http\Server $server
     * @param Server $horseLoft
     */
    protected function createRedisQueue()
    {
        if (empty($this->container()->getRedisQueueData())) {
            return;
        }

        foreach ($this->container()->getRedisQueueData() as $queue) {
            $process = new \Swoole\Process(function () use ($queue){
                if ("Darwin" != PHP_OS) {
                    swoole_set_process_name('redis_subscribe_process:' . $queue['channel']);
                }
                \Library\Core\Structure\Redis\RedisSubscribe::subscribe($queue['config'], $queue['channel']);
            });
            $this->container()->getServer()->addProcess($process);
        }
    }


    /*
     *
     *
     *
     * --------------------------------------------------------------------------
     *  set
     * --------------------------------------------------------------------------
     *
     *
     *
     *
     */

    /**
     * --------------------------------------------------------------------------
     *  数据库配置信息
     * --------------------------------------------------------------------------
     *
     * @return bool
     */
    protected function setDatabaseConfigure()
    {
        $config = $this->readConfigFile('database');

        $this->container()->setConfigure('database', $config);

        return true;
    }

    /**
     * --------------------------------------------------------------------------
     *  拦截器
     * --------------------------------------------------------------------------
     *
     * @return bool
     */
    protected function setInterceptorConfigure()
    {
        $config = $this->readConfigFile('interceptor');

        $interceptor = [];

        foreach ($config as $key => $value) {
            $configure = $this->getCallback($value);
            if ($configure == false) {
                continue;
            }
            $interceptor[$key] = $configure;
        }

        if (!empty($interceptor)) {
            $this->container()->setConfigure('interceptor', $interceptor);
        }

        return true;
    }


    /*
     *
     *
     *
     * --------------------------------------------------------------------------
     *  read and set
     * --------------------------------------------------------------------------
     *
     *
     *
     */


    /**
     * --------------------------------------------------------------------------
     *  设置全局配置信息
     * --------------------------------------------------------------------------
     *
     * @param Server $horseLoft
     * @param array $names
     * @param string $path
     * @return bool
     */
    protected function readSetConfigure(array $names, string $path)
    {
        foreach ($names as $filename) {
            $file = $path . '/' .$filename . '.php';
            if (!is_file($file)) {
                continue;
            }

            try {
                $configure = require_once $file;

                if (!is_array($configure)) {
                    continue;
                }
                $this->container()->setConfigure($filename, $configure);

            } catch (\Exception $e){
                continue;
            }
        }
        return true;
    }

    /**
     *
     * @param Server $horseLoft
     * @param array $names
     * @return bool
     */
    protected function readSetRoute(array $names)
    {
        foreach ($names as $filename) {
            $file = HORSE_LOFT_SERVICE_PATH . '/Route/' .$filename . '.php';
            if (!is_file($file)) {
                continue;
            }

            try {
                $routeConfig = require_once $file;

                if (!is_array($routeConfig) || empty($routeConfig['route']) || !is_array($routeConfig['route'])) {
                    continue;
                }

                if (empty($routeConfig['namespace'])) {
                    $namespace = '';
                } else {
                    $namespace = trim($routeConfig['namespace'], '\\') . '\\';
                }

                $interceptor = empty($routeConfig['interceptor']) || !is_string($routeConfig['interceptor']) ? '' : $routeConfig['interceptor'];

                foreach ($routeConfig['route'] as $key => $route) {
                    if (!is_string($key) || empty($route) || !is_string($route) || strpos($route, '@') == false) {
                        continue;
                    }
                    $path = explode('@', $route);

                    if (empty($routeConfig['prefix'])) {
                        $uri = trim($key, '/');
                    } else {
                        $uri = trim($routeConfig['prefix'], '/') . '/' . trim($key, '/');
                    }

                    $this->container()->setRouteConfig($uri,
                        [
                            'controller' => $namespace . $path[0],
                            'function' => $path[1],
                            'interceptor' => $interceptor
                        ]
                    );
                }

            } catch (\Exception $e){
                continue;
            }
        }
        return true;
    }

    /**
     * ------------------------------------------------------------
     *  读取配置文件
     * ------------------------------------------------------------
     *
     * 配置文件返回值应该是数组
     *
     * @param string $filename
     * @return array
     */
    private function readConfigFile(string $filename)
    {
        $filename = '/' . $filename . '.php';

        $data = [];
        $config = [];

        //读取全局配置
        if (is_file(HORSE_LOFT_CONFIG_PATH . $filename)) {
            try {
                $data = require_once HORSE_LOFT_CONFIG_PATH . $filename;
                if (!empty($data) && is_array($data)) {
                    $config = $data;
                }
            } catch (\Exception $e){

            }
        }

        //读取当前项目配置
        if (is_file(HORSE_LOFT_SERVICE_PATH . '/Config' . $filename)) {
            try {
                $info = require_once HORSE_LOFT_SERVICE_PATH . '/Config' . $filename;
                if (!empty($info) && is_array($info)) {
                    $config = array_merge($data, $info);
                }
            } catch (\Exception $e){

            }
        }

        return $config;
    }

    /**
     * ------------------------------------------------------------
     *  生成可用的回调方法
     * ------------------------------------------------------------
     *
     * $config = [
     *  'namespace' => 'Controller',
     *  'callback' => 'DemoController@myFunction'
     * ]
     *
     * @param array $config
     * @return array|callable|false
     */
    protected function getCallback(array $config)
    {
        if (empty($config['callback']) || !is_string($config['callback']) || strpos($config['callback'], '@') == false) {
            return false;
        }

        $call = explode('@', $config['callback']);

        $namespace = rtrim(rtrim(HORSE_LOFT_CONTROLLER_NAMESPACE, '\\'), 'Controller');

        if (!empty($config['namespace'])) {
            $namespace .= trim($config['namespace'], '\\') . '\\';
        }
        $namespace .= trim($call[0], '\\');

        $callback = [$namespace, $call[1]];
        if (!is_callable($callback)) {
            return false;
        }
        return $callback;
    }

}
