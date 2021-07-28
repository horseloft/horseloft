<?php
/**
 * Date: 2021/5/12 09:35
 * User: YHC
 * Desc: redis代理
 */

namespace Library\Core\Structure\Redis;

use Library\Pool\Redis;

class RedisConnector
{
    /**
     * @var \Redis
     */
    protected $redis;

    protected $key;

    /**
     * RemoteDictionary constructor.
     * @param \Redis $redis
     */
    public function __construct(\Redis $redis)
    {
        $this->redis = $redis;
    }

    public function __destruct()
    {
        Redis::$pool[$this->key][] = $this->redis;
    }

    /**
     *
     * @return \Redis
     */
    public function redis(string $key)
    {
        $this->key = $key;

        return $this->redis;
    }
}