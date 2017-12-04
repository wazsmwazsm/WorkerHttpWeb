<?php
require_once 'PDODMLTest.php';
use Framework\DB\Drivers\Pgsql;

class PgsqlDMLTest extends PDODMLTest
{

    public function getConnection()
    {
        // pdo 对象，用于测试被测对象和构建测试基境
        if (self::$pdo == null) {
            $dsn = 'pgsql:dbname=test;host=localhost;port=5432';
            self::$pdo = new PDO($dsn, 'homestead', 'secret');
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
        }
        // 待测的 mysql 对象
        if (self::$db == null) {
            self::$db = new Pgsql('localhost', '5432', 'homestead', 'secret', 'test', 'utf8');
        }

        return $this->createDefaultDBConnection(self::$pdo, ':memory:');
    }
}
