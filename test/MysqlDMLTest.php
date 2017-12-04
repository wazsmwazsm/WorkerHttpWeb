<?php
require_once 'PDODMLTest.php';
use Framework\DB\Drivers\Mysql;

class MysqlDMLTest extends PDODMLTest
{

    public function getConnection()
    {
        // pdo 对象，用于测试被测对象和构建测试基境
        if (self::$pdo == null) {
            $dsn = 'mysql:dbname=test;host=localhost;port=3306';
            self::$pdo = new PDO($dsn, 'homestead', 'secret', [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8']);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        }
        // 待测的 mysql 对象
        if (self::$db == null) {
            self::$db = new Mysql('localhost', '3306', 'homestead', 'secret', 'test', 'utf8');
        }

        return $this->createDefaultDBConnection(self::$pdo, $dsn);
    }
}
