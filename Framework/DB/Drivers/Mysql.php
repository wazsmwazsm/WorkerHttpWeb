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

    private $_pdo = NULL;
    /**
     * PDOStatement instance
     *
     * @var \PDOStatement
     */
    private $_pdoSt = NULL;
    private $_config = [];

    private $_table = '';
    private $_query_sql = '';

    private $_cols_str = ' * ';
    private $_where_str = '';
    private $_orderby_str = '';
    private $_groupby_str = '';
    private $_having_str = '';
    private $_bind_params = [];

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
        $this->_orderby_str = '';
        $this->_groupby_str = '';
        $this->_having_str = '';
        $this->_bind_params = [];
    }

    private function _buildQuery() {
        $this->_query_sql = 'SELECT '.$this->_cols_str.' FROM '.$this->_table.
            $this->_where_str.$this->_groupby_str.$this->_orderby_str.$this->_having_str;
    }

    private function _bindParams() {
        if(is_array($this->_bind_params)) {
            foreach ($this->_bind_params as $plh => $param) {
                $this->_pdoSt->bindValue($plh, $param);
            }
        }
    }

    private static function _backquote($str) {
        // match pattern
        $alias_pattern = '/([a-zA-Z0-9_]+)\s+(AS|as|As)\s+([a-zA-Z0-9_]+)/';
        $alias_replace = '`$1` AS `$3`';
        $func_pattern = '/[a-zA-Z0-9_]+\([a-zA-Z0-9_\,\s\`\'\"\*]*\)/';
        // alias mode
        if(preg_match($alias_pattern, $str)) {
            return preg_replace($alias_pattern, $alias_replace, $str);
        }
        // mysql fun mode
        if(preg_match($func_pattern, $str)) {
            return $str;
        }
        // field mode
        return '`'.$str.'`';
    }

    public function select() {
        $cols = func_get_args();

        if( ! func_num_args() || in_array('*', $cols)) {
            $this->_cols_str = ' * ';
        } else {
            $this->_cols_str = '';
            foreach ($cols as $col) {
                $this->_cols_str .= ' '.self::_backquote($col).',';
            }
            $this->_cols_str = rtrim($this->_cols_str, ',');
        }

        return $this;
    }


    public function get() {
        $this->_buildQuery();
        var_dump($this->_query_sql);
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

    // 条件构造的统一操作
    private function _condition_constructor($args_num, $params, $operator, &$construct_str) {

        // params dose not conform to specification
        if( ! $args_num || $args_num > 3) {
            throw new PDOException("Error number of parameters");
        }

        switch ($args_num) {
          case 1:
              if( ! is_array($params[0])) {
                  throw new PDOException($params[0].' should be Array');
              }
              foreach ($params[0] as $field => $value) {
                  $plh = ':'.bin2hex($field.'_'.$value);
                  $construct_str .= ' '.self::_backquote($field).' = '.$plh.' '.$operator;
                  $this->_bind_params[$plh] = $value;
              }
              // 想想有没有更好的处理方案
              $construct_str = rtrim($construct_str, $operator);
              break;
          case 2:
              $plh = ':'.bin2hex($params[0].'_'.$params[1]);
              $construct_str .= ' '.self::_backquote($params[0]).' = '.$plh.' ';
              $this->_bind_params[$plh] = $params[1];
              break;
          case 3:
              if( ! in_array($params[1], ['<', '>', '<=', '>=', '=', '!=', '<>'])) {
                  throw new PDOException('Confusing Symbol '.$params[1]);
              }
              $plh = ':'.bin2hex($params[0].'_'.$params[1].'_'.$params[2]);
              $construct_str .= ' '.self::_backquote($params[0]).' '.$params[1].' '.$plh.' ';
              $this->_bind_params[$plh] = $params[2];
              break;
        }
    }

    public function where() {

        $operator = 'AND';

        // is the first time call where method ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE ';
        } else {
            $this->_where_str .= ' '.$operator.' ';
        }

        $this->_condition_constructor(func_num_args(), func_get_args(), $operator, $this->_where_str);

        return $this;
    }

    public function orWhere() {

        $operator = 'OR';

        // is the first time call where method ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE ';
        } else {
            $this->_where_str .= ' '.$operator.' ';
        }

        $this->_condition_constructor(func_num_args(), func_get_args(), $operator, $this->_where_str);

        return $this;
    }

    public function groupBy($field) {
        // is the first time call groupBy method ?
        if($this->_groupby_str == '') {
            $this->_groupby_str = ' GROUP BY '.self::_backquote($field);
        } else {
            $this->_groupby_str .= ' , '.self::_backquote($field);
        }

        return $this;
    }

    public function having() {
        $operator = 'AND';

        // is the first time call where method ?
        if($this->_having_str == '') {
            $this->_having_str = ' HAVING ';
        } else {
            $this->_having_str .= ' '.$operator.' ';
        }

        $this->_condition_constructor(func_num_args(), func_get_args(), $operator, $this->_having_str);

        return $this;
    }

    public function orHaving() {
        $operator = 'OR';

        // is the first time call where method ?
        if($this->_having_str == '') {
            $this->_having_str = ' HAVING ';
        } else {
            $this->_having_str .= ' '.$operator.' ';
        }

        $this->_condition_constructor(func_num_args(), func_get_args(), $operator, $this->_having_str);

        return $this;
    }

    public function orderBy($field, $mode) {
        // is the first time call orderBy method ?
        if($this->_orderby_str == '') {
            $this->_orderby_str = ' ORDER BY '.self::_backquote($field).' '.$mode;
        } else {
            $this->_orderby_str .= ' , '.self::_backquote($field).' '.$mode;
        }

        return $this;
    }

    public function query($sql) {
        return $this->_pdo->query($sql);
    }

    public function insert() {

    }

    public function update() {

    }

    public function delete() {

    }

}
