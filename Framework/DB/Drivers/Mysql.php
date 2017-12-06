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
        extract($this->_config, EXTR_SKIP);

        $dsn = 'mysql:dbname='.$dbname.
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
                $this->_pdo->prepare("set names $charset")->execute();
            }
            // timezone
            if(isset($timezone)) {
                $this->_pdo->prepare("set time_zone='$timezone'")->execute();
            }

        } catch (PDOException $e) {
            throw $e;
        }
    }

}
