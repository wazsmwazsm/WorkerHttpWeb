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
     * PDOStatement instance
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
        $dsn = 'mysql:dbname='.$this->_config['dbname'].
               ';host='.$this->_config['host'].
               ';port='.$this->_config['port'];

        try {
            $this->_pdo = new PDO(
                $dsn,
                $this->_config['user'],
                $this->_config['password'],
                [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$this->_config['charset']]
            );
            // set error mode
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // disables emulation of prepared statements
            $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage());
        }
    }


    public function table($table) {
        $this->_table = $table;
        return $this;
    }

    // memory-resident mode , need manual reset attr
    private function _reset() {
        $this->_table = '';
        $this->_query_sql = '';
        $this->_cols_str = ' * ';
        $this->_where_str = '';
        $this->_bind_params = [];
    }

    private function _buildQuery() {
        $this->_query_sql = 'SELECT '.$this->_cols_str.' FROM '.$this->_table.$this->_where_str;
    }

    private function _bindParams() {
        if(is_array($this->_bind_params)) {
            foreach ($this->_bind_params as $plh => $param) {
                $this->_pdoSt->bindValue($plh, $param);
            }
        }
    }

    public function select() {
        $cols = func_get_args();

        if( ! func_num_args() || in_array('*', $cols)) {
            $this->_cols_str = ' * ';
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
        $this->_buildQuery();
        var_dump($this->_query_sql,$this->_bind_params);
        $this->_pdoSt = $this->_pdo->prepare($this->_query_sql);
        $this->_bindParams();

        $this->_reset();
        $this->_pdoSt->execute();
        return $this->_pdoSt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function row() {
        $this->_buildQuery();

        $this->_pdoSt = $this->_pdo->prepare($this->_query_sql);
        $this->_bindParams();

        $this->_reset();
        $this->_pdoSt->execute();
        return $this->_pdoSt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert() {

    }

    public function update() {

    }

    public function delete() {

    }

    public function where() {
        $args_num = func_num_args();
        $params   = func_get_args();

        // params dose not conform to specification
        if( ! $args_num || $args_num > 3) {
            return $this;
        }

        // is the first time call where method ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE ';
        } else {
            $this->_where_str .= ' AND ';
        }

        switch ($args_num) {
          case 1:
              if( ! is_array($params[0])) {
                  throw new PDOException($params[0].' should be Array');
              }
              foreach ($params[0] as $field => $value) {
                  $plh = ':'.bin2hex($field.'_'.$value);
                  $this->_where_str .= ' '.$field.' = '.$plh.' AND';
                  $this->_bind_params[$plh] = $value;
              }
              // 想想有没有更好的处理方案
              $this->_where_str = rtrim($this->_where_str, 'AND');
              break;
          case 2:
              $plh = ':'.bin2hex($params[0].'_'.$params[1]);
              $this->_where_str .= ' '.$params[0].' = '.$plh.' ';
              $this->_bind_params[$plh] = $params[1];
              break;
          case 3:
              if( ! in_array($params[1], ['<', '>', '<=', '>=', '=', '!=', '<>'])) {
                  throw new PDOException('Confusing Symbol '.$params[1]);
              }
              $plh = ':'.bin2hex($params[0].'_'.$params[1].'_'.$params[2]);
              $this->_where_str .= ' '.$params[0].' '.$params[1].' '.$plh.' ';
              $this->_bind_params[$plh] = $params[2];
              break;
        }

        return $this;
    }

    public function group() {

    }

    public function having() {

    }

    public function orderBy() {

    }
}
