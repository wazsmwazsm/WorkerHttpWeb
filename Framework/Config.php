<?php
namespace Framework;

/**
 * Config.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Config {

    /**
     * config.
     *
     * @var Array
     */
    public static $config = [];

    /**
     * get config.
     *
     * @param  string  $key
     * @return void
     */
    public static function get($key) {

        $path = explode('.', $key);
        list($file, $conf) = [$path[0], $path[1]];

        return self::$config[$file][$conf];
    }
}
