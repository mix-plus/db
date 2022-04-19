<?php

namespace MixPlus\Db\Cache;

class Cache implements CacheInterface
{
    public static function set(string $key, string $value, int $timeout = 0): bool
    {
        return redis()->set($key, $value, $timeout);
    }

    public static function get(string $key): bool|string
    {
        return redis()->get($key);
    }

    public static function has(string $key): bool
    {
        return (bool)redis()->get($key);
    }

    public static function del(...$key): int
    {
        return redis()->del($key);
    }

    public static function getOrSet(string $key, callable $callable): bool|string
    {
        $value = redis()->get($key);
        if (!$value) {
            $value = $callable();
            redis()->set($key, $value, 3600);
        }
        return $value;
    }
}
