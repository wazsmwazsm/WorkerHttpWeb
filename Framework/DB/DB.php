<?php
namespace Framework\DB;
use Framework\Config;
use Framework\DB\Mysql;
/**
 * DB.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class DB {

    public static $connection = [];

    public static function init() {
        $db_confs = Config::get('database.connection');
        foreach ($db_confs as $con_name => $db_conf) {
            switch (strtolower($db_conf['driver'])) {
              case 'mysql':
                self::$connection[$con_name] = new Mysql
                (
                    $db_conf['host'],
                    $db_conf['port'],
                    $db_conf['user'],
                    $db_conf['password'],
                    $db_conf['dbname'],
                    $db_conf['charset']
                );
                break;
              default:
                break;
            }

        }
    }
}
