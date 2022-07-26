<?php

namespace MixPlus\Db\Cache;

use MixPlus\Db\Redis\BaseRedis;
use MixPlus\Db\Redis\Redis;

class Cache implements CacheInterface
{
    /**
     * @var Redis 
     */
    protected $redis;
    
    public function __construct()
    {
        $this->redis = new BaseRedis(config('redis'));    
    }

    public function set(string $key, string $value, int $timeout = 0): bool
    {
        return $this->redis->set($key, $value, $timeout);
    }

    public function get(string $key): bool|string
    {
        return $this->redis->get($key);
    }

    public function has(string $key): bool
    {
        return (bool)$this->redis->get($key);
    }

    public function del(...$key): int
    {
        return $this->redis->del($key);
    }

    public function getOrSet(string $key, callable $callable): bool|string
    {
        $value = $this->redis->get($key);
        if (!$value) {
            $value = $callable();
            $this->redis->set($key, $value, 3600);
        }
        return $value;
    }
}
