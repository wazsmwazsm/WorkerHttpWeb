<?php
namespace Framework\DB\Drivers;
use PDO;
use PDOException;
use Framework\DB\Drivers\PDODriver;

/**
 * Mysql Driver
 *
 * @author MirQin https://github.com/wazsmwazsm
 */
class Pgsql extends PDODriver implements ConnectorInterface
{

    /**
     * escape symbol
     *
     * @var array
     */
    protected static $_escape_symbol = '"';

    /**
     * operators
     *
     * @var array
     */
    protected $_operators = [
      '=', '<', '>', '<=', '>=', '<>', '!=',
      'like', 'not like', 'ilike', 'similar to', 'not similar to',
      '&', '|', '#', '<<', '>>',
    ];

    /**
     * The default PDO connection options.
     *
     * @var array
     */
    protected $_options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        PDO::ATTR_STRINGIFY_FETCHES => false,
    ];

    /**
     * create a PDO instance
     *
     * @return  void
     * @throws  \PDOException
     */
    protected function _connect()
    {
        extract($this->_config, EXTR_SKIP);

        $dsn = 'pgsql:dbname='.$dbname.
               ';host='.$host.
               ';port='.$port;

        $options = isset($options) ? $options + $this->_options : $this->_options;

        try {
            $this->_pdo = new PDO(
                $dsn,
                $user,
                $password,
                $options
            );
            // charset set
            if(isset($charset)) {
                $this->_pdo->prepare("set names '$charset'")->execute();
            }
            // set schema path
            if(isset($schema)) {
                $this->_pdo->prepare("set search_path to $schema")->execute();
            }

        } catch (PDOException $e) {
            throw $e;
        }
    }


}
