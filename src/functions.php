<?php

if (!function_exists('redis')) {
    function redis()
    {
        return new \MixPlus\Db\Redis\BaseRedis(config('redis'));
    }
}

if (!function_exists('db')) {
    function db()
    {
        // return \MixPlus\Db\Container\DB::instance();
    }
}