<?php
/**
 * Date: 2021/5/11 19:04
 * User: YHC
 * Desc: redis 连接池 worker级别
 */

namespace Library\Pool;

use Library\Core\Structure\Redis\RedisBuilder;
use Library\Core\Structure\Redis\RedisConnector;
use Library\Utils\Helper;

class Redis
{
    use RedisBuilder;

    //连接池
    public static $pool = [];

    /**
     * ----------------------------------------------------------------
     *  获取Redis连接
     * ----------------------------------------------------------------
     *
     * $config = [
     *  'host' => 'localhost',   // redis host | 必填项
     *  'port' => 6379,          // redis host | 必填项
     *  'database' => 0,         // redis database | 选填项 默认等于0
     *  'password' => '',        // redis password | 选填项 默认无密码
     * ]
     *
     * $database 选填项 | 如果指定当前参数 则会替换$config中的database值
     *
     * @param int|null $database
     * @return \Redis
     */
    public static function connect(array $config, int $database = null)
    {
        $remote = new RedisConnector(self::getConnect($config));

        $redis = $remote->redis(self::getKey($config));

        if (!is_null($database) && $redis->select($database) == false) {
            throw new \RuntimeException('redis select database failed', HORSE_LOFT_REDIS_ERROR_CODE);
        }

        return $redis;
    }

    /**
     * ----------------------------------------------------------------
     * 向当前服务的Redis消息发布
     * ----------------------------------------------------------------
     *
     * 备份：
     *  仅当 $isBackup = true 时
     *  发布失败的数据 或者 消费函数返回值不全等于true时 会在Redis中以列表的的形式保留最新的10000条 key=channel:key
     *  Redis中备份数据 key = horseloft_redis_queue_backup:channel:md5(json_encode(, JSON_UNESCAPED_UNICODE))
     *
     * $callback:
     *  必须是可以在消费端被回调的类的静态方法
     *  例：
     *      'ClassName::methodName'
     *      '\Services\DemoService\Controller\DemoController::crontabEcho',
     *      [\Services\DemoService\Controller\DemoController::class, 'crontabEcho']
     *
     * @param string $channel 频道名称
     * @param mixed $data 发布的消息数据
     * @param mixed $callback 处理发布消息数据的程序
     * @param bool $isBackup 是否开启数据备份
     * @return bool
     */
    public static function publish(string $channel, $data, $callback, bool $isBackup = true)
    {
        $redis = self::getConnect(Helper::getQueueRedisConfig($channel));
        $message = [
            'callback' => $callback,
            'data' => $data
        ];
        return self::publishMessage($channel, $message, $isBackup, $redis);
    }

    /**
     * ----------------------------------------------------------------
     * 向指定的Redis中发布消息
     * ----------------------------------------------------------------
     *
     * $config = [
     *  'host' => 'localhost',   // redis host | 必填项
     *  'port' => 6379,          // redis host | 必填项
     *  'database' => 0,         // redis database | 选填项 默认等于0
     *  'password' => '',        // redis password | 选填项 默认无密码
     * ]
     *
     * @param string $channel
     * @param $data
     * @param callable $callback
     * @param array $config Redis连接配置信息
     * @param bool $isBackup
     * @return bool
     */
    public static function publicByConfig(string $channel, $data, callable $callback, array $config, bool $isBackup = true)
    {
        $message = [
            'callback' => $callback,
            'data' => $data
        ];
        return self::publishMessage($channel, $message, $isBackup, self::getConnect($config));
    }

    /**
     * ----------------------------------------------------------------
     * redis 消息订阅
     * ----------------------------------------------------------------
     *
     * $config = [
     *  'host' => 'localhost',   // redis host | 必填项
     *  'port' => 6379,          // redis host | 必填项
     *  'database' => 0,         // redis database | 选填项 默认等于0
     *  'password' => '',        // redis password | 选填项 默认无密码
     * ]
     *
     * @param array $config
     * @param string $channel
     */
    final public static function subscribe(array $config, string $channel)
    {
        return self::subscribeMessage($config, $channel);
    }
}
