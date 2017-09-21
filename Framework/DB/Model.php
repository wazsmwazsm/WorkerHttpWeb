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

    protected $db;

    public function __construct() {
        $this->db = DB::$connection[$this->connection]->table($this->connection);
    }

    public function __call($method, $params)
    {
        return $this->db->$method($params);
    }

}
