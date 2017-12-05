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
        $dsn = 'pgsql:dbname='.$this->_config['dbname'].
               ';host='.$this->_config['host'].
               ';port='.$this->_config['port'];
        try {
            $this->_pdo = new PDO(
                $dsn,
                $this->_config['user'],
                $this->_config['password'],
                $this->_getOptions()
            );

            $this->_pdo->prepare("set names '{$this->_config['charset']}'")->execute();

        } catch (PDOException $e) {
            throw $e;
        }
    }


}
