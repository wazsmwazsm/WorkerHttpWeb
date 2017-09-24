<?php
namespace Framework\DB;

use Framework\DB\DB;
/**
 * DB.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Model
{

    protected $connection;

    protected $table;

    public static function __callStatic($method, $params)
    {
        $instance = new static;
        
        $db = DB::$connection[$instance->connection]->table($instance->table);
        // improve the efficiency
        switch (count($params)) {
            case 0:
                return $db->$method();
            case 1:
                return $db->$method($params[0]);
            case 2:
                return $db->$method($params[0], $params[1]);
            case 3:
                return $db->$method($params[0], $params[1], $params[2]);
            case 4:
                return $db->$method($params[0], $params[1], $params[2], $params[3]);
            default:
                return call_user_func_array([$db, $method], $params);
        }
    }

    public function __call($method, $params)
    {
        $db = DB::$connection[$this->connection]->table($this->table);
        // improve the efficiency
        switch (count($params)) {
            case 0:
                return $db->$method();
            case 1:
                return $db->$method($params[0]);
            case 2:
                return $db->$method($params[0], $params[1]);
            case 3:
                return $db->$method($params[0], $params[1], $params[2]);
            case 4:
                return $db->$method($params[0], $params[1], $params[2], $params[3]);
            default:
                return call_user_func_array([$db, $method], $params);
        }
    }


}
