<?php
/**
 * Date: 2021/5/22 03:30
 * User: YHC
 * Desc:
 */

namespace Library\Core\Structure\Redis;

class RedisSubscribe
{
    use RedisBuilder;

    /**
     * ----------------------------------------------------------------
     * redis 消息发布
     * ----------------------------------------------------------------
     *
     * @param array $config
     * @param string $channel
     */
    final public static function subscribe(array $config, string $channel)
    {
        return self::subscribeMessage($config, $channel);
    }
}