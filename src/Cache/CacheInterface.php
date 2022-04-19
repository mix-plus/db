<?php

namespace MixPlus\Db\Cache;

interface CacheInterface
{
    public static function set(string $key, string $value, int $timeout = 0): bool;

    public static function get(string $key);

    public static function has(string $key);

    public static function del(string $key): int;

    public static function getOrSet(string $key, callable $callable);
}
