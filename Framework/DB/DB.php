<?php
namespace Framework\DB;
use Framework\Config;
use Framework\DB\Drivers\Mysql;
/**
 * DB.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class DB {
    /**
     * connections.
     *
     * @var Array
     */
    public static $connection = [];

    /**
     * init db connections.
     *
     * @return void
     */
    public static function init() {
        // get db config
        $db_confs = Config::get('database.connection');
        // connect database
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
