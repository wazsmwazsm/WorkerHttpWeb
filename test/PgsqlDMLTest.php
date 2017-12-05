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
            $options = [
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
                PDO::ATTR_STRINGIFY_FETCHES => false,
            ];
            self::$pdo = new PDO($dsn, 'homestead', 'secret', $options);
            self::$pdo->prepare("set names 'utf8'")->execute();
        }
        // 待测的 mysql 对象
        if (self::$db == null) {
            self::$db = new Pgsql('localhost', '5432', 'homestead', 'secret', 'test', 'utf8');
        }

        return $this->createDefaultDBConnection(self::$pdo, $dsn);
    }
}
