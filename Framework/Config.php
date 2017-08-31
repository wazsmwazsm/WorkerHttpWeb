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
     * set config.
     *
     * @param  string  $file
     * @param  string  $conf
     * @return void
     */
    public static function set($file, $conf) {

        self::$config[$file] = $conf;
    }

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
