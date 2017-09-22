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

    protected static $instance;


    public static function getInstance() {
        if( ! static::$instance instanceof static) {
            static::$instance = new static;
        }
        return static::$instance;
    }

    public static function __callStatic($method, $params)
    {
        if(static::$instance instanceof static) {
            $instance = static::$instance;
        } else {
            $instance = new static;
        }
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
