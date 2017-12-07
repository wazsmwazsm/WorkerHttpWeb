<?php
require_once 'PDODQLTest.php';
use Framework\DB\Drivers\Sqlite;

class SqliteDQLTest extends PDODQLTest
{
    public static function setUpBeforeClass()
    {
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

        // 被测对象
        $config = [
          'dbname' => '/home/vagrant/Code/WorkerPHPWeb/test/test.db',
          'prefix' => 't_',
        ];
        self::$db = new Sqlite($config);
    }

}
