<?php
/**
 * Date: 2021/5/12 09:37
 * User: YHC
 * Desc:
 */

namespace Library\Core\Structure\Redis;

use Library\Core\Horseloft\Spanner;
use Library\Utils\Helper;
use Library\Utils\Log;

trait RedisBuilder
{
    /**
     *
     * @return \Redis
     */
    private static function getConnect(array $config)
    {
        $key = self::getKey($config);

        if (!empty(self::$pool[$key])) {
            return array_shift(self::$pool[$key]);
        }

        return self::redis($config);
    }

    /**
     *
     * @return string
     */
    private static function getKey(array $config)
    {
        return md5(serialize($config));
    }

    /**
     * redis connect
     *
     * @param array $config
     * @return \Redis
     */
    private static function redis(array $config)
    {
        if (empty($config)) {
            throw new \RuntimeException('redis config error', HORSE_LOFT_REDIS_ERROR_CODE);
        }
        if (empty($config['host'])) {
            throw new \RuntimeException('empty host in redis config', HORSE_LOFT_REDIS_ERROR_CODE);
        }
        if (empty($config['port'])) {
            throw new \RuntimeException('empty port in redis config', HORSE_LOFT_REDIS_ERROR_CODE);
        }

        $redis = new \Redis();

        if ($redis->connect($config['host'], $config['port']) == false) {
            throw new \RuntimeException('redis connection failed', HORSE_LOFT_REDIS_ERROR_CODE);
        }

        if (!empty($config['password']) && $redis->auth($config['password']) == false) {
            throw new \RuntimeException('redis connection failed with password', HORSE_LOFT_REDIS_ERROR_CODE);
        }

        if (!empty($config['database']) && $redis->select(intval($config['database'])) == false) {
            throw new \RuntimeException('redis select database failed', HORSE_LOFT_REDIS_ERROR_CODE);
        }

        $redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);

        return $redis;
    }

    /**
     * 消息发布
     *
     * @param string $channel
     * @param array $message
     * @param bool $isBackup
     * @param \Redis|null $redis
     * @return bool
     */
    private static function publishMessage(string $channel, array $message, bool $isBackup = false, \Redis $redis = null)
    {
        if (empty($message['callback'])) {
            throw new \RuntimeException('empty queue callback', HORSE_LOFT_REDIS_ERROR_CODE);
        }
        //用于存储备份消息用的Redis key
        $message['key'] = md5(Spanner::encode($message['callback']));

        if (empty($channel)) {
            throw new \RuntimeException('empty queue channel', HORSE_LOFT_REDIS_ERROR_CODE);
        }
        $message['channel'] = $channel;

        //是否备份数据 如果=true 在消费失败时，将会把数据存储，但只备份最新的一万条数据
        $message['backup'] = $isBackup;

        //数据被订阅端接收
        if ($redis->publish($message['channel'], Spanner::encode($message)) > 0) {
            return true;
        }

        //不开启数据备份
        if ($isBackup == false) {
            return false;
        }

        //数据备份 新数据插入到尾部 只备份最新的一万条数据
        return self::queueBackup($message, $redis);
    }

    /**
     * 创建频道订阅
     *
     * @param array $config
     * @param string $channel
     * @param \Redis|null $redis
     */
    private static function subscribeMessage(array $config, string $channel, \Redis $redis = null)
    {
        if (is_null($redis)) {
            $redis = self::redis($config);
        }

        $redis->subscribe([$channel], function ($instance, $channel, $message) use ($redis, $config) {
            /*
             * $message json数组
             * $message = [
             *  'channel' => 'string',  // 频道名称
             *  'key' => 'string',      // 使用key 在redis中生成存储信息，存储格式key=channel:key,value=data
             *  'callback' => [],       // 消费数据时 执行的回调；回调应该是完整的带有namespace的类地址
             *  'data' => 'data',       // 队列数据，除资源外的任意格式
             *  'backup' => true        // 是否备份数据；如果回调返回值为false则写入备份
             * ]
             */
            $data = json_decode($message, true);
            
            if ((empty($data['callback']) || !is_callable($data['callback'])) && $data['backup']) {
                //则将数据写入备份记录中
                self::queueBackup($data, null, $config);
                return;
            }

            $param = empty($data['data']) ? [] : [$data['data']];

            /*
             * 如果回调出错,则无法捕捉返回值；可能的错误：
             *  1.参数错误
             *  2.回调的方法抛出了异常
             * 如果回调返回值 !==true 则将数据写入备份记录中
             *
             * 如果开启了数据备份 则将备份信息写入Redis 如果写入Redis失败 则日志记录
             */
            if (call_user_func_array($data['callback'], $param) !== true
                && $data['backup']
                && self::queueBackup($data, null, $config) == false
            ) {
                Log::recording(Helper::getLogPath() . '/' . 'queue',
                    json_encode(array_merge(['level' => 'error'], $data))
                );
            }
        });
    }

    /**
     * 数据写入备注
     *
     * @param string $key
     * @param array $message
     * @param \Redis $redis
     * @return bool
     */
    private static function queueBackup(array $message, \Redis $redis = null, array $config = [])
    {
        if (is_null($redis)) {
            $redis = self::redis($config);
        }
        unset($message['backup']);

        $redisKey = 'horseloft_redis_queue_backup:' . $message['channel'] . ':' . $message['key'];

        //数据备份 新数据插入到尾部
        $length = $redis->rpush($redisKey, Spanner::encode($message));
        if ($length === false) {
            return false;
        }

        //只备份最新的一万条数据 移除多余的数据
        if ($length > 10000) {
            $redis->lTrim($redisKey, $length - 10000, $length);
        }
        return true;
    }
}