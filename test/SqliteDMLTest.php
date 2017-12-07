<?php
require_once 'PDODMLTest.php';
use Framework\DB\Drivers\Sqlite;

class SqliteDMLTest extends PDODMLTest
{

    public function getConnection()
    {
        // pdo 对象，用于测试被测对象和构建测试基境
        if (self::$pdo == null) {
            // 新建 pdo 对象, 用于测试被测驱动
            $dsn = 'sqlite:/home/vagrant/Code/WorkerPHPWeb/test/test.db';
            $options = [
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
                PDO::ATTR_STRINGIFY_FETCHES => FALSE,
                PDO::ATTR_EMULATE_PREPARES => FALSE,
            ];
            self::$pdo = new PDO($dsn, '', '', $options);
        }
        // 待测的 mysql 对象
        if (self::$db == null) {
            $config = [
              'dbname' => '/home/vagrant/Code/WorkerPHPWeb/test/test.db',
              'prefix' => 't_',
            ];
            self::$db = new Sqlite($config);
        }

        return $this->createDefaultDBConnection(self::$pdo, $dsn);
    }
}
