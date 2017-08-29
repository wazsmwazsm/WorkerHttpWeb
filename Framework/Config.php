<?php
namespace Framework;

class Config {
    public static $config = [];

    public static function get($key) {
        $path = explode('.', $key);
        list($file, $conf) = [$path[0], $path[1]];

        return self::$config[$file][$conf];
    }
}
