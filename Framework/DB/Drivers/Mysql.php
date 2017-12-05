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
class Mysql extends PDODriver implements ConnectorInterface
{
    /**
     * escape symbol
     *
     * @var array
     */
    protected static $_escape_symbol = '`';

    /**
     * operators
     *
     * @var array
     */
    protected $_operators = [
      '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
      'like', 'not like', 'like binary', 'rlike', 'regexp', 'not regexp',
      '&', '|', '^', '<<', '>>',
    ];

    /**
     * create a PDO instance
     *
     * @return  void
     * @throws  \PDOException
     */
    protected function _connect()
    {
        $dsn = 'mysql:dbname='.$this->_config['dbname'].
               ';host='.$this->_config['host'].
               ';port='.$this->_config['port'].
               ';charset='.$this->_config['charset'];
        try {
            $this->_pdo = new PDO(
                $dsn,
                $this->_config['user'],
                $this->_config['password'],
                $this->_getOptions()
            );

            $this->_pdo->prepare('set names '.$this->_config['charset'])->execute();

        } catch (PDOException $e) {
            throw $e;
        }
    }

}
