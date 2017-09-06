<?php
namespace Framework\DB\Drivers;
use PDO;
/**
 * Mysql, $ use https://github.com/walkor/mysql.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Mysql implements ConnectorInterface {

    private $_pdo;
    private $_config;

    private $_table;



    public function __construct($host, $port, $user, $password, $dbname, $charset = 'utf8') {
        $this->_config = [
            'host'     => $host,
            'port'     => $port,
            'user'     => $user,
            'password' => $password,
            'dbname'   => $db_name,
            'charset'  => $charset,
        ];
        $this->_connect();
    }

    private function _connect() {
        $dsn = 'mysql:dbname='.$this->_config["dbname"].
               ';host='.$this->_config["host"].
               ';port='.$this->_config['port'];

        $this->_pdo = new PDO(
            $dsn,
            $this->_config["user"],
            $this->_config["password"],
            [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$this->_config['charset'])]
        );
        // 错误时抛出异常
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // 不使用 php 本地函数进行预处理，使用数据库的预处理
        $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
    }

    public function table($table) {
        $this->_table = $table;
    }

    private function _buildQuery() {

    }

    public function select() {

    }

    public function query($sql) {
        return $this->pdo->query($sql);
    }

    public function get() {

    }

    public function row() {

    }

    public function insert() {

    }

    public function update() {

    }

    public function delete() {

    }

    public function where() {

    }

    public function group() {

    }

    public function having() {

    }

    public function orderBy() {

    }
}
