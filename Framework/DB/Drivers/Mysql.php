<?php
namespace Framework\DB\Drivers;
use PDO;
use PDOException;
/**
 * Mysql, $ use https://github.com/walkor/mysql.
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Mysql implements ConnectorInterface {

    private $_pdo;
    /**
     * PDOStatement 实例
     *
     * @var \PDOStatement
     */
    private $_pdoSt;
    private $_config;

    private $_table;
    private $_query_sql;

    private $_cols_str = ' * ';
    private $_where_str;

    private $_bind_params;

    public function __construct($host, $port, $user, $password, $dbname, $charset = 'utf8') {
        $this->_config = [
            'host'     => $host,
            'port'     => $port,
            'user'     => $user,
            'password' => $password,
            'dbname'   => $dbname,
            'charset'  => $charset,
        ];
        $this->_connect();
    }

    private function _connect() {
        $dsn = 'mysql:dbname='.$this->_config["dbname"].
               ';host='.$this->_config["host"].
               ';port='.$this->_config['port'];

        try {
            $this->_pdo = new PDO(
                $dsn,
                $this->_config["user"],
                $this->_config["password"],
                [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$this->_config['charset']]
            );
            // 错误时抛出异常
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // 不使用 php 本地函数进行预处理，使用数据库的预处理
            $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }

    public function table($table) {
        $this->_table = $table;
        return $this;
    }

    private function _buildQuery() {
        $this->_query_sql = "SELECT ".$this->_cols_str." FROM ".$this->_table.$this->_where_str;
    }

    private function _bindParams() {
        if(is_array($this->_bind_params)) {
            foreach ($this->_bind_params as $plh => $param) {
                $this->_pdoSt->bindParam($plh, $param);
            }
        }
    }

    public function select() {
        $cols = func_get_args();

        if( ! func_num_args() || in_array('*', $cols)) {
            $this->_cols_str = " * ";
        } else {
            foreach ($cols as $col) {
                $this->_cols_str .= ' '.$col.',';
            }
            $this->_cols_str = rtrim($this->_cols_str, ',');
        }

        return $this;
    }

    public function query($sql) {
        return $this->_pdo->query($sql);
    }

    public function get() {
        $this->_buildQuery();var_dump($this->_query_sql);

        $this->_pdoSt = $this->_pdo->prepare($this->_query_sql);
        $this->_bindParams();
        $this->_pdoSt->execute();
        return $this->_pdoSt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function row() {
        $this->_buildQuery();

        $this->_pdoSt = $this->_pdo->prepare($this->_query_sql);
        $this->_bindParams();
        $this->_pdoSt->execute();
        return $this->_pdoSt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert() {

    }

    public function update() {

    }

    public function delete() {

    }

    public function where(Array $params) {
        if($this->_where_str == '') {
            $this->_where_str = " WHERE ";
        } else {
            $this->_where_str .= " AND ";
        }

        foreach ($params as $field => $value) {
            $this->_where_str .= " $field = :$field AND";
            $this->_bind_params[":$field"] = $value;
        }
        // 想想有没有更好的处理方案
        $this->_where_str = rtrim($this->_where_str, 'AND');

        return $this;
    }

    public function group() {

    }

    public function having() {

    }

    public function orderBy() {

    }
}
