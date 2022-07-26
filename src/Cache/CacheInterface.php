<?php

namespace MixPlus\Db\Cache;

interface CacheInterface
{
    public function set(string $key, string $value, int $timeout = 0): bool;

    public function get(string $key);

    public function has(string $key);

    public function del(string $key): int;

    public function getOrSet(string $key, callable $callable);
}
