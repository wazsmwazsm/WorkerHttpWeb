<?php
namespace Framework\DB\Drivers;
use PDO;
use PDOException;
use Closure;
/**
 * Mysql Driver
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Mysql implements ConnectorInterface
{

    private $_pdo = NULL;
    /**
     * PDOStatement instance
     *
     * @var \PDOStatement
     */
    private $_pdoSt = NULL;
    private $_config = [];

    private $_buildAttrs = [
      '_table',
      '_query_sql',
      '_cols_str',
      '_where_str',
      '_orderby_str',
      '_groupby_str',
      '_having_str',
      '_join_str',
      '_limit_str',
    ];

    private $_table = '';
    private $_query_sql = '';

    private $_cols_str = ' * ';
    private $_where_str = '';
    private $_orderby_str = '';
    private $_groupby_str = '';
    private $_having_str = '';
    private $_join_str = '';
    private $_limit_str = '';

    private $_insert_str = '';
    private $_update_str = '';

    private $_bind_params = [];

    public function __construct($host, $port, $user, $password, $dbname, $charset = 'utf8')
    {
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

    private function _connect()
    {
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
            throw $e;
        }
    }

    private function _closeConnection()
    {
        $this->_pdo = NULL;
    }


    // memory-resident mode , need manual reset attr
    private function _reset()
    {
        $this->_table = '';
        $this->_query_sql = '';
        $this->_cols_str = ' * ';
        $this->_where_str = '';
        $this->_orderby_str = '';
        $this->_groupby_str = '';
        $this->_having_str = '';
        $this->_join_str = '';
        $this->_limit_str = '';
        $this->_insert_str = '';
        $this->_update_str = '';
        $this->_bind_params = [];
    }

    private function _resetBuildStr()
    {
        $this->_table = '';
        $this->_query_sql = '';
        $this->_cols_str = ' * ';
        $this->_where_str = '';
        $this->_orderby_str = '';
        $this->_groupby_str = '';
        $this->_having_str = '';
        $this->_join_str = '';
        $this->_limit_str = '';
    }

    private function _buildQuery()
    {
        $this->_query_sql = 'SELECT '.$this->_cols_str.' '.' FROM '.$this->_table.
            $this->_join_str.
            $this->_where_str.
            $this->_groupby_str.$this->_having_str.
            $this->_orderby_str.
            $this->_limit_str;
    }

    private function _buildInsert()
    {
        $this->_query_sql = 'INSERT INTO '.$this->_table.$this->_insert_str;
    }

    private function _buildUpdate()
    {
        $this->_query_sql = 'UPDATE '.$this->_table.$this->_update_str.$this->_where_str;
    }

    private function _buildDelete()
    {
        $this->_query_sql = 'DELETE FROM '.$this->_table.$this->_where_str;
    }

    private function _execute()
    {
        try {
            $this->_pdoSt = $this->_pdo->prepare($this->_query_sql);
            $this->_bindParams();
            $this->_reset();  // memory-resident mode, singleton pattern, need reset build attr
            $this->_pdoSt->execute();

        } catch (PDOException $e) {
            // when time out, reconnect
            if($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {
                $this->_closeConnection();
                $this->_connect();
                // retry
                try {
                    $this->_pdoSt = $this->_pdo->prepare($this->_query_sql);
                    $this->_bindParams();
                    $this->_reset();
                    $this->_pdoSt->execute();

                } catch (PDOException $e) {
                    throw $e;
                }

            } else {
                throw $e;
            }
        }

    }

    private function _bindParams()
    {
        if(is_array($this->_bind_params)) {
            foreach ($this->_bind_params as $plh => $param) {
                $this->_pdoSt->bindValue($plh, $param);
            }
        }
    }

    private static function _getPlh()
    {
        return ':'.uniqid();
    }

    private static function _backquote($str)
    {
        // match pattern
        $alias_pattern = '/([a-zA-Z0-9_\.]+)\s+(AS|as|As)\s+([a-zA-Z0-9_]+)/';
        $alias_replace = '`$1` $2 `$3`';
        $prefix_pattern = '/([a-zA-Z0-9_]+\s*)(\.)(\s*[a-zA-Z0-9_]+)/';
        $prefix_replace = '`$1`$2`$3`';
        $func_pattern = '/[a-zA-Z0-9_]+\([a-zA-Z0-9_\,\s\`\'\"\*]*\)/';
        // alias mode
        if(preg_match($alias_pattern, $str, $alias_match)) {
            // if field is aa.bb as cc mode
            if(preg_match($prefix_pattern, $alias_match[1])) {
                $pre_rst = preg_replace($prefix_pattern, $prefix_replace, $alias_match[1]);
                $alias_replace = $pre_rst.' $2 `$3`';
            }
            return preg_replace($alias_pattern, $alias_replace, $str);
        }
        // prefix mode
        if(preg_match($prefix_pattern, $str)) {
            return preg_replace($prefix_pattern, $prefix_replace, $str);
        }
        // mysql fun mode
        if(preg_match($func_pattern, $str)) {
            return $str;
        }
        // field mode
        return '`'.$str.'`';
    }

    private function _condition_constructor($args_num, $params, $operator, &$construct_str)
    {
        // params dose not conform to specification
        if( ! $args_num || $args_num > 3) {
            throw new \InvalidArgumentException("Error number of parameters");
        }
        // argurment mode
        switch ($args_num) {
          // assoc array mode
          case 1:
              if( ! is_array($params[0])) {
                  throw new \InvalidArgumentException($params[0].' should be Array');
              }
              foreach ($params[0] as $field => $value) {
                  $plh = self::_getPlh();
                  $construct_str .= ' '.self::_backquote($field).' = '.$plh.' '.$operator;
                  $this->_bind_params[$plh] = $value;
              }
              // 想想有没有更好的处理方案
              $construct_str = rtrim($construct_str, $operator);
              break;
          // ('a', 10) : a = 10 mode
          case 2:
              $plh = self::_getPlh();
              $construct_str .= ' '.self::_backquote($params[0]).' = '.$plh.' ';
              $this->_bind_params[$plh] = $params[1];
              break;
          // ('a', '>', 10) : a > 10 mode
          case 3:
              if( ! in_array($params[1], ['<', '>', '<=', '>=', '=', '!=', '<>'])) {
                  throw new \InvalidArgumentException('Confusing Symbol '.$params[1]);
              }
              $plh = self::_getPlh();
              $construct_str .= ' '.self::_backquote($params[0]).' '.$params[1].' '.$plh.' ';
              $this->_bind_params[$plh] = $params[2];
              break;
        }
    }

    private function _storeAttr()
    {
        // attribute need to store
        $store = [];
        // store attr
        foreach ($this->_buildAttrs as $buildAttr) {
            $store[ltrim($buildAttr, '_')] = $this->$buildAttr;
        }

        return $store;
    }

    private function _reStoreAttr(Array $data)
    {
        foreach ($this->_buildAttrs as $buildAttr) {
            $this->$buildAttr = $data[ltrim($buildAttr, '_')];
        }
    }

    private function _storeBindParam()
    {
        return $this->_bind_params;
    }

    private function _reStoreBindParam($bind_params)
    {
        $this->_bind_params = $bind_params;
    }

    private function _subBuilder(Closure $callback)
    {
        // store build attr
        $store = $this->_storeAttr();

        /**************** begin sub query build ****************/
            // empty attribute
            $this->_resetBuildStr();
            // call sub query callback
            call_user_func($callback, $this);
            // get sub query build attr
            $sub_attr = [];

            $this->_buildQuery();

            foreach ($this->_buildAttrs as $buildAttr) {
                $sub_attr[ltrim($buildAttr, '_')] = $this->$buildAttr;
            }
        /**************** end sub query build ****************/

        // restore attribute
        $this->_reStoreAttr($store);

        return $sub_attr;
    }


    public function table($table)
    {
        $this->_table = self::_backquote($table);
        
        return $this;
    }

    public function select()
    {
        $cols = func_get_args();

        if( ! func_num_args() || in_array('*', $cols)) {
            $this->_cols_str = ' * ';
        } else {
            // _cols_str default ' * ' , it easy to get a result when select func dosen't called
            // but when you call select func , you should set it to ''
            $this->_cols_str = '';
            foreach ($cols as $col) {
                $this->_cols_str .= ' '.self::_backquote($col).',';
            }
            $this->_cols_str = rtrim($this->_cols_str, ',');
        }

        return $this;
    }


    public function where()
    {
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

    public function orWhere()
    {
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

    public function whereIn($field, Array $data, $condition = 'IN', $operator = 'AND')
    {
        if( ! in_array($condition, ['IN', 'NOT IN']) || ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Error whereIn mode");
        }
        // create placeholder
        foreach ($data as $key => $value) {
            $plh = self::_getPlh();
            $data[$key] = $plh;
            $this->_bind_params[$plh] = $value;
        }
        // is the first time call where method ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE '.self::_backquote($field).' '.$condition.' ('.implode(',', $data).')';
        } else {
            $this->_where_str .= ' '.$operator.' '.self::_backquote($field).' '.$condition.' ('.implode(',', $data).')';
        }

        return $this;
    }

    public function orWhereIn($field, Array $data)
    {
        return $this->whereIn($field, $data, 'IN', 'OR');
    }

    public function whereNotIn($field, Array $data)
    {
        return $this->whereIn($field, $data, 'NOT IN', 'AND');
    }

    public function orWhereNotIn($field, Array $data)
    {
        return $this->whereIn($field, $data, 'NOT IN', 'OR');
    }

    public function whereBetween($field, $start, $end, $operator = 'AND')
    {
        if( ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Logical operator");
        }
        // create placeholder
        $start_plh = self::_getPlh();
        $end_plh = self::_getPlh();
        $this->_bind_params[$start_plh] = $start;
        $this->_bind_params[$end_plh] = $end;

        // is the first time call where method ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE '.self::_backquote($field).' BETWEEN '.$start_plh.' AND '.$end_plh;
        } else {
            $this->_where_str .= ' '.$operator.' '.self::_backquote($field).' BETWEEN '.$start_plh.' AND '.$end_plh;
        }

        return $this;
    }

    public function orWhereBetween($field, $start, $end)
    {
        return $this->whereBetween($field, $start, $end, 'OR');
    }

    public function whereNull($field, $condition = 'NULL', $operator = 'AND')
    {
        if( ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Logical operator");
        }
        // is the first time call where method ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE ';
        } else {
            $this->_where_str .= ' '.$operator.' ';
        }

        $this->_where_str .= self::_backquote($field).' IS '.$condition.' ';

        return $this;
    }

    public function whereNotNull($field)
    {
        return $this->whereNull($field, 'NOT NULL', 'AND');
    }

    public function orWhereNull($field)
    {
        return $this->whereNull($field, 'NULL', 'OR');
    }

    public function orWhereNotNull($field)
    {
        return $this->whereNull($field, 'NOT NULL', 'OR');
    }

    public function whereBrackets(Closure $callback, $operator = 'AND')
    {
        if( ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Logical operator");
        }
        // first time call where ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE ( ';
        } else {
            $this->_where_str .= ' '.$operator.' ( ';
        }
        $sub_attr = $this->_subBuilder($callback);

        $this->_where_str .= preg_replace('/WHERE/', '', $sub_attr['where_str'], 1).' ) ';

        return $this;
    }

    public function orWhereBrackets(Closure $callback)
    {
        return $this->whereBrackets($callback, 'OR');
    }

    public function whereExists(Closure $callback, $condition = 'EXISTS', $operator = 'AND')
    {
        if( ! in_array($condition, ['EXISTS', 'NOT EXISTS']) || ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Error whereExists mode");
        }
        // first time call where ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE '.$condition.' ( ';
        } else {
            $this->_where_str .= ' '.$operator.' '.$condition.' ( ';
        }

        $sub_attr = $this->_subBuilder($callback);
        $this->_where_str .= $sub_attr['query_sql'].' ) ';

        return $this;
    }

    public function whereNotExists(Closure $callback)
    {
        return $this->whereExists($callback, 'NOT EXISTS', 'AND');
    }

    public function orWhereExists(Closure $callback)
    {
        return $this->whereExists($callback, 'EXISTS', 'OR');
    }
    public function orWhereNotExists(Closure $callback)
    {
        return $this->whereExists($callback, 'NOT EXISTS', 'OR');
    }

    public function whereInSub($field, Closure $callback, $condition = 'IN', $operator = 'AND')
    {
        if( ! in_array($condition, ['IN', 'NOT IN']) || ! in_array($operator, ['AND', 'OR'])) {
            throw new \InvalidArgumentException("Error whereIn mode");
        }
        // first time call where ?
        if($this->_where_str == '') {
            $this->_where_str = ' WHERE '.self::_backquote($field).' '.$condition.' ( ';
        } else {
            $this->_where_str .= ' '.$operator.' '.self::_backquote($field).' '.$condition.' ( ';
        }

        $sub_attr = $this->_subBuilder($callback);
        $this->_where_str .= $sub_attr['query_sql'].' ) ';

        return $this;
    }

    public function whereNotInSub($field, Closure $callback)
    {
        return $this->whereInSub($field, $callback, 'NOT IN', 'AND');
    }

    public function orWhereInSub($field, Closure $callback)
    {
        return $this->whereInSub($field, $callback, 'IN', 'OR');
    }

    public function orWhereNotInSub($field, Closure $callback)
    {
        return $this->whereInSub($field, $callback, 'NOT IN', 'OR');
    }



    public function groupBy($field)
    {
        // is the first time call groupBy method ?
        if($this->_groupby_str == '') {
            $this->_groupby_str = ' GROUP BY '.self::_backquote($field);
        } else {
            $this->_groupby_str .= ' , '.self::_backquote($field);
        }

        return $this;
    }

    public function having()
    {
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

    public function orHaving()
    {
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

    public function orderBy($field, $mode = 'ASC')
    {
        if( ! in_array($mode, ['ASC', 'DESC'])) {
            throw new \InvalidArgumentException("Error order by mode");
        }
        // is the first time call orderBy method ?
        if($this->_orderby_str == '') {
            $this->_orderby_str = ' ORDER BY '.self::_backquote($field).' '.$mode;
        } else {
            $this->_orderby_str .= ' , '.self::_backquote($field).' '.$mode;
        }

        return $this;
    }

    public function join($table, $one, $two, $type = 'INNER')
    {
        if( ! in_array($type, ['INNER', 'LEFT', 'RIGHT'])) {
            throw new \InvalidArgumentException("Error join mode");
        }
        // create join string
        $this->_join_str .= ' '.$type.' JOIN '.self::_backquote($table).
            ' ON '.self::_backquote($one).' = '.self::_backquote($two);
        return $this;
    }

    public function leftJoin($table, $one, $two)
    {
        return $this->join($table, $one, $two, 'LEFT');
    }

    public function rightJoin($table, $one, $two)
    {
        return $this->join($table, $one, $two, 'RIGHT');
    }

    public function fromSub(Closure $callback)
    {
        $sub_attr = $this->_subBuilder($callback);
        $this->_table .= ' ( '.$sub_attr['query_sql'].' ) AS tb_'.uniqid().' ';

        return $this;
    }

    public function limit($offset, $step)
    {
        $this->_limit_str = ' LIMIT '.$offset.' , '.$step.' ';

        return $this;
    }

    public function paginate($step, $page = NULL)
    {
        // store build attr\bind param
        $store = $this->_storeAttr();
        $bind_params = $this->_storeBindParam();
        // get count
        $count = $this->count();
        // restore build attr\bind param
        $this->_reStoreAttr($store);
        $this->_reStoreBindParam($bind_params);

        // create paginate data
        $page = $page ? $page : 1;
        $this->limit($step * ($page - 1), $step);

        $rst['total']        = $count;
        $rst['per_page']     = $step;
        $rst['current_page'] = $page;var_dump($count / $step);
        $rst['next_page']    = ($page + 1) > ($count / $step) ? NULL : ($page + 1);
        $rst['prev_page']    = ($page - 1) < 1 ? NULL : ($page - 1);
        $rst['first_page']   = 1;
        $rst['last_page']    = $count / $step;
        $rst['data']         = $this->get();

        return $rst;
    }

    public function get()
    {
        $this->_buildQuery();
        $this->_execute();

        return $this->_pdoSt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function row()
    {
        $this->_buildQuery();
        $this->_execute();

        return $this->_pdoSt->fetch(PDO::FETCH_ASSOC);
    }

    public function list($field)
    {
        $this->_cols_str = ' `'.$field.'` ';
        $this->_buildQuery();
        $this->_execute();

        return $this->_pdoSt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function count($field = '*')
    {
        if(trim($field) != '*') {
            $field = '`'.$field.'`';
        }
        $this->_cols_str = ' COUNT('.$field.') AS count_num ';

        return $this->row()['count_num'];
    }

    public function sum($field)
    {
        $this->_cols_str = ' SUM(`'.$field.'`) AS sum_num ';

        return $this->row()['sum_num'];
    }

    public function max($field)
    {
        $this->_cols_str = ' MAX(`'.$field.'`) AS max_num ';

        return $this->row()['max_num'];
    }

    public function min($field)
    {
        $this->_cols_str = ' MIN(`'.$field.'`) AS min_num ';

        return $this->row()['min_num'];
    }

    public function avg($field)
    {
        $this->_cols_str = ' AVG(`'.$field.'`) AS avg_num ';

        return $this->row()['avg_num'];
    }

    public function insert(Array $data)
    {
        // create build str
        $field_str = '';
        $value_str = '';
        foreach ($data as $key => $value) {
            $field_str .= ' '.self::_backquote($key).',';
            $plh = self::_getPlh();
            $this->_bind_params[$plh] = $value;
            $value_str .= ' '.$plh.',';
        }

        $field_str = rtrim($field_str, ',');
        $value_str = rtrim($value_str, ',');

        $this->_insert_str = ' ('.$field_str.') VALUES ('.$value_str.') ';
        // execute
        $this->_buildInsert();
        $this->_execute();

        if($this->_pdoSt->rowCount() > 0) {
            return $this->_pdo->lastInsertId();
        }

        return NULL;
    }

    public function update(Array $data)
    {
        // should not update without where
        if(empty($this->_where_str)) {
            throw new \InvalidArgumentException("Need where condition");
        }
        // create build str
        $this->_update_str = ' SET ';
        foreach ($data as $key => $value) {
            $plh = self::_getPlh();
            $this->_bind_params[$plh] = $value;
            $this->_update_str .= ' '.self::_backquote($key).' = '.$plh.',';
        }

        $this->_update_str = rtrim($this->_update_str, ',');

        $this->_buildUpdate();
        $this->_execute();

        return $this->_pdoSt->rowCount();
    }

    public function delete()
    {
        // should not delete without where
        if(empty($this->_where_str)) {
            throw new \InvalidArgumentException("Need where condition");
        }

        $this->_buildDelete();
        $this->_execute();

        return $this->_pdoSt->rowCount();
    }


    public function query($sql)
    {
        try {
            return $this->_pdo->query($sql);
        } catch (PDOException $e) {
            // when time out, reconnect
            if($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {

                $this->_closeConnection();
                $this->_connect();

                try {
                    return $this->_pdo->query($sql);
                } catch (PDOException $e) {
                    throw $e;
                }

            } else {
                throw $e;
            }
        }
    }

    public function exec($sql)
    {
        try {
            return $this->_pdo->exec($sql);
        } catch (PDOException $e) {
            // when time out, reconnect
            if($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {

                $this->_closeConnection();
                $this->_connect();

                try {
                    return $this->_pdo->exec($sql);
                } catch (PDOException $e) {
                    throw $e;
                }

            } else {
                throw $e;
            }
        }
    }

    public function prepare($sql, Array $driver_options = [])
    {
        try {
            return $this->_pdo->prepare($sql, $driver_options);
        } catch (PDOException $e) {
            // when time out, reconnect
            if($e->errorInfo[1] == 2006 || $e->errorInfo[1] == 2013) {

                $this->_closeConnection();
                $this->_connect();

                try {
                    return $this->_pdo->prepare($sql, $driver_options);
                } catch (PDOException $e) {
                    throw $e;
                }

            } else {
                throw $e;
            }
        }
    }
}
